<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Admin;
use App\Models\Order;
use App\Models\Product;
use App\Models\Seller;
use App\Models\User;
use App\Traits\AuthenticateTrait;
use App\Traits\OrderTrait;
use Illuminate\Support\Facades\File;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    use AuthenticateTrait;


    function check(Request $request){
         //Validate Inputs
         $request->validate([
            'email'=>'required|email|exists:admins,email',
            'password'=>'required|min:5|max:30'
         ],[
             'email.exists'=>'This email is not exists.'
         ]);

        $creds = $request->only('email','password');

        $check_active_admin =  DB::table('admins')->where('email',$request->email)->first();
        if($check_active_admin)
        {
            if($check_active_admin->is_active == '1'){
                if( Auth::guard('admin')->attempt($creds) ){
                    session()->put('login_type', 'admin');
                    session()->put('admin_id', Auth::guard('admin')->user()->id);
                    return redirect()->route('admin.home');
                }
                else
                {
                    return redirect()->route('admin.login')->with('fail','Incorrect credentials');
                }
            } else {
                return redirect()->route('admin.login')->with('fail','Your Account is Inactive');
            }
        }
        else
        {
            return redirect()->route('admin.login')->with('fail','Incorrect credentials');
        }
    }

    function logout(){
        Auth::guard('admin')->logout();
        return redirect('/');
    }

    public function Dashboard(){
        $dashboard  = [];
        $dashboard['counts']['products']    =   Product::where('status','1')->count();
        $dashboard['counts']['sellers']     =   Seller::where('is_active','1')->where('approval','1')->count();
        $dashboard['counts']['customers']   =   User::where('is_active','1')->count();
        $dashboard['counts']['orders']      =   Order::whereNotNull('transaction_id')->count();

        $dashboard['popular_products']      =   Product::where('status','1')
                                                ->leftjoin('sellers','sellers.id','=','products.seller_id')
                                                ->join(DB::raw('(SELECT
                                                        OV.productid, count(OV.productid) as productorders
                                                        FROM
                                                        order_vendors as OV
                                                        GROUP BY OV.productid
                                                        ) AS OV_table'),
                                                function($join)
                                                {
                                                    $join->on('products.id', '=', 'OV_table.productid');
                                                })

                                                ->select('products.id','products.image','products.name','sellers.seller_full_name_buss as sellername','OV_table.productorders')
                                                ->orderBy('OV_table.productorders','desc')->paginate(6);

        $dashboard['popular_sellers']       =   DB::table('sellers')->where('is_active','1')->where('approval','1')
                                                ->join(DB::raw('(SELECT
                                                        OV.sellerid, count(OV.sellerid) as sellerorders
                                                        FROM
                                                        order_vendors as OV
                                                        GROUP BY OV.sellerid
                                                        ) AS OV_table'),
                                                        function($join)
                                                        {
                                                        $join->on('sellers.id', '=', 'OV_table.sellerid');
                                                    })
                                                ->select('sellers.id','sellers.seller_full_name_buss as name','sellers.sellerprofile as image',
                                                'OV_table.sellerorders','sellers.created_at')
                                                ->orderBy('OV_table.sellerorders','desc')->paginate(6);

        $dashboard['order_vendors']         =   DB::table('order_vendors')
                                                ->select('order_vendors.*', 'products.name as productname', 'sellers.sellername',
                                                'order_statuses.name as statusname','products.image as productimage',
                                                'sellers.sellerprofile as sellerimage')
                                                ->join('products','order_vendors.productid','=','products.id')
                                                ->join('order_statuses','order_statuses.id','=','order_vendors.orderstatus')
                                                ->join('sellers','sellers.id','=','order_vendors.sellerid')
                                                ->orderBy('order_vendors.created_at','desc')
                                                ->paginate(10);
        return view('dashboard.admin.home',compact('dashboard'));
    }


    public function ForgetPassword(Request $request){
        try {
            $request->validate([
                'email'=>'required|email|exists:admins,email',
             ],[
                 'email.exists'=>'This email is not exists.'
             ]);

            $user_details   =   Admin::where('email',$request->email)->first();
            $mail           =   $request->email;
            $name           =   $user_details->name;
            $usertype       =   "Admin";
            if($user_details->is_active != '1'){
                throw new Exception("Account is Inactive. Please Contact Admin");
            }
            if($user_details->forget_pass_token == '1'){
                throw new Exception("Mail Already Sent. Please Check.");
            }
            $SendForgetMail =   $this->SendForgetMail($mail,$name,$usertype);
            if($SendForgetMail['status'] == true)
            {
                $result     =   ['status' => true, 'message'=>"Mail Sent, Please Check."];
            }
            else
            {
                throw new Exception($SendForgetMail['message']);
            }
        } catch (\Exception $e) {
            $result =   ['status' => false, 'message'=>$e->getMessage()];
        }
        if($result['status'] == true)
        {
            return redirect()->route('admin.login')->with('success',$result['message']);
        }
        else
        {
            return redirect()->back()->with('fail',$result['message']);
        }
        return response()->json($result);
    }

    public function UpdateProfile()
    {
        try
        {
            $admin_id       =   auth()->guard('admin')->user()->id;
            $UpdateData     =   request()->except('_token');
            $VerifiedUser   =   Admin::where('id', $admin_id)
                                ->where('is_active','=','1')->first();
            if(request()->has('profile') && request()->profile != '')
            {
                $profile    =   request()->profile;
                $img_url    =   "Admin/".$admin_id."-".str_replace(' ','-', strtolower($VerifiedUser->name));
                if($VerifiedUser->profile != null){
                    if(File::exists($VerifiedUser->profile)){
                        Storage::disk('s3')->delete($VerifiedUser->profile);
                    }
                }
                $path = Storage::disk('s3')->put($img_url, $profile);
                $product_path = Storage::disk('s3')->url($path);

                $UpdateData['profile'] = $product_path;

            }

            $UpdateData['updated_at'] = date('Y-m-d H:i:s');
            unset($UpdateData['id']);

            $updateprofile  =   Admin::where('id', $admin_id)->update($UpdateData);
            if($updateprofile){
                $result     =   ['status'=>true, 'message'=>"Profile Updated Succesfully"];
            }else{
                throw new Exception("Something Went Wrong in Updating");
            }
        } catch (\Exception $e) {
            $result =   ['status'=>false, 'message'=>$e->getMessage()];
        }
        if($result['status'] == true)
            return back()->with('success',$result['message']);
        else
            return back()->with('error',$result['message']);
    }

    public function UpdatePassword(){
        try
        {
            $validator = Validator::make(request()->all(),[
                'old_password'=>'required',
                'password' => 'required|confirmed',
            ]);

            if($validator->fails())
            {
                $result = ['status'=>false,'message'=> implode( ", ",$validator->errors()->all())];
            }
            else
            {
                $email          =   auth()->guard('admin')->user()->email;
                $password       =   Hash::make(request()->password);
                $VerifiedUser   =   Admin::where('email', $email)
                                    ->where('is_active','=','1')->first();
                if(!$VerifiedUser){
                    throw new Exception("Please Check Email You Provided");
                }
                if(Hash::check(request()->old_password, $VerifiedUser->password)){
                    Admin::where('email',$email)->update(['password'=>$password,'updated_at'=>date('Y-m-d H:i:s')]);
                    $result     =   ['status'=>true, 'message'=>"Password Updated Succesfully"];
                }else{
                    throw new Exception("Please Check the Old Password");
                }
            }
        } catch (\Exception $e) {
            $result =   ['status'=>false, 'message'=>$e->getMessage()];
        }
        if($result['status'] == true){
            Auth::guard('admin')->logout();
            return redirect('/');
        }
        else{
            return back()->with('error',$result['message']);
        }
    }
}
