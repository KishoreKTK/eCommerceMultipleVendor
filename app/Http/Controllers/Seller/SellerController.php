<?php

namespace App\Http\Controllers\Seller;
use Session;
use App\Http\Controllers\Controller;
use App\Models\OrderVendor;
use App\Models\Product;
use App\Models\ProductStocks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Seller;
use App\Models\SellerCategories;
use App\Traits\AuthenticateTrait;
use App\Traits\CategoryTrait;
use App\Traits\ShopTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SellerController extends Controller
{
    use CategoryTrait,ShopTrait, CategoryTrait, AuthenticateTrait;

    function registerpage(){
        $categories     =   $this->SelectCategoryList();
        $uae_cities     =   $this->UaeLargestCities();
        return view('dashboard.seller.register',compact('categories','uae_cities'));
    }

    function create(Request $request)
    {
        //Validate inputs
        $request->validate([
            'sellername'=>'required',
            'selleremail'=>'required|email|unique:sellers',
            'password'=>'required|min:5|max:30',
            'password_confirmation'=>'required|min:5|max:30|same:password'
        ]);


        $input = $request->except('_token','password_confirmation','category_id','password','SellerProfile','seller_trade_license');
        // dd($input);
        $name               = $request->sellername;
        $sellerimageurl     = 'Sellers/'.str_replace(' ', '_', $name).'/profile';
        $sellerlicense      = 'Sellers/'.str_replace(' ', '_', $name).'/tradelicense';
        // $sellername         = time().'_'.str_replace(' ', '_',$request->file('SellerProfile')->getClientOriginalName());
        // $tradename          = time().'_'.str_replace(' ', '_',$request->file('seller_trade_license')->getClientOriginalName());

        // $request->SellerProfile->move(public_path($sellerimageurl), $sellername);

        // $request->seller_trade_license->move(public_path($sellerlicense), $tradename);

        $path = Storage::disk('s3')->put($sellerimageurl, $request->SellerProfile);
        $profile_path = Storage::disk('s3')->url($path);

        if(request()->has('seller_trade_license') && request()->seller_trade_license == null){
            $licensepath = Storage::disk('s3')->put($sellerlicense, $request->seller_trade_license);
            $sellerlicense_path = Storage::disk('s3')->url($licensepath);
        } else {
            $sellerlicense_path =   "-";
        }


        $input['sellerprofile']         =   $profile_path;
        $input['seller_trade_license']  =   $sellerlicense_path;
        $input['password']              =   Hash::make($request->password);
        $input['approval']              =   '0';
        $input['is_active']             =   '0';
        $input['pickup']             =   '0';
        $input['delivery']             =   '0';
        $input['is_active']             =   '0';
        $input['cash_on_delivery']             =   '0';
        // print("<pre>");print_r($input);die;
        $seller  = Seller::create($input);

        if( $seller ){
            $sellerid = $seller->id;
            // Insert Shop Categories
            $seller_cat_data    =   $this->InsertShopCategories($sellerid,request()->category_id);
            SellerCategories::insert($seller_cat_data);
            return redirect()->back()->with('success','Thanks For Registering, We will Contact You Soon');
        } else {
            return redirect()->back()->with('fail','Something went Wrong, failed to register');
        }
    }

    public function if_seller_updated()
    {
        $seller     =   Auth::guard('seller')->user();
        $sellerid   =   Auth::guard('seller')->user()->id;
        $messages   =   [];
        $check=0;
        // if Trade License Expired Not Shown
        if(strtotime($seller->seller_trade_exp_dt) < strtotime('now') && $seller->seller_trade_exp_dt!=Null){
            $check=$check+1;
        }
        // If no Latitude and Longitude Available
        if($seller->latitdue == null && $seller->longitude == null){
           $check=$check+1;
        }
        // If no Delivery and Pickup Available Not Shown
        if($seller->pickup == 0 && $seller->delivery == 0){
            $check=$check+1;
        }
        // if Delivery Yes and No Delivery Area Updated Not Shown
        if($seller->delivery == 1){
            $check_shipping_locations = DB::table('seller_shipping_details')->where('seller_id',$sellerid)->count();
            if($check_shipping_locations == 0){
                $check=$check+1;
            }
        }
        // if No Products Available don't Show
        $check_avail_products = DB::table('products')->where('seller_id',$sellerid)
                                ->where('status','1')->count();
        if($check_avail_products == 0){
            $check=$check+1;
        }
        return $check;
    }

    function check(Request $request)
    {
        $request->validate([
           'selleremail'=>'required|email|exists:sellers',
           'password'=>'required|min:5|max:30'
        ],[
            'selleremail.exists'=>'This email is not exists in sellers table'
        ]);

        $userDetails =  Seller::where('selleremail', $request->selleremail)
                        ->where('approval','=','1')->first();
        if($userDetails)
        {
            if($userDetails->is_active == '1'){
                $creds  = ['selleremail' => $request->selleremail, 'password' => $request->password];
                if( Auth::guard('seller')->attempt($creds) ){
                    session()->put('login_type', 'seller');
                    session()->put('seller_id', Auth::guard('seller')->user()->id);

                    //check whether seller has updated all the necessary docs
                    $docs_updated=$this->if_seller_updated();
                    if($docs_updated>0)
                    {
                        return redirect()->route('seller.EditSellerOrderSettingsPage',[Auth::guard('seller')->user()->id]);
                    }else{
                        return redirect()->route('seller.home');
                    }
                }
                else
                {
                    return redirect()->route('seller.login')->with('fail','Incorrect Credentials');
                }
            } else {
                return redirect()->route('seller.login')->with('fail','Your Account is Inactive');
            }
        }
        else{
            return redirect()->route('seller.login')->with('fail','Incorrect Credentials.');
        }
    }

    public function Dashboard()
    {
        $seller_id      =   session()->get('seller_id');
        $dashboard      =   [];
        $dashboard['counts']['categories']  =   SellerCategories::where('seller_id',$seller_id)->where('status','2')->count();
        $dashboard['counts']['products']    =   Product::where('seller_id',$seller_id)->where('status','1')->count();
        $dashboard['counts']['orders']      =   OrderVendor::where('sellerid',$seller_id)->count();
        $revenue    =       OrderVendor::where('sellerid',$seller_id)
                            ->select(DB::raw('SUM(order_vendors.total_amount) as totalrevenue'))
                            ->groupBy('order_vendors.sellerid')->first();
        if($revenue) {
            $totalrevenue   = $revenue->totalrevenue;
        }else {
            $totalrevenue   = 0;
        }
        $dashboard['counts']['revenues']    =   $totalrevenue;

        $dashboard['seller']['profile']     =   Seller::where('id',$seller_id)->first();
        $dashboard['seller']['stocks']      =   ProductStocks::leftjoin('products','products.id','=','product_stocks.product_id')
                                                ->select('product_stocks.product_id as id','products.name','product_stocks.quantities',
                                                'product_stocks.product_price')
                                                ->where('products.seller_id',$seller_id)->get();
        $dashboard['seller']['products']    =   Product::where('products.seller_id',$seller_id)
                                                ->where('products.status','=','1')->paginate(6);

        $dashboard['order_vendors']         =   DB::table('order_vendors')
                                                ->select('order_vendors.*', 'products.name as productname', 'sellers.sellername',
                                                'order_statuses.name as statusname','products.image as productimage', 'orders.id as ordertblid',
                                                'sellers.sellerprofile as sellerimage')
                                                ->leftJoin('orders','orders.order_id','order_vendors.orderid')
                                                ->join('products','order_vendors.productid','=','products.id')
                                                ->join('order_statuses','order_statuses.id','=','order_vendors.orderstatus')
                                                ->join('sellers','sellers.id','=','order_vendors.sellerid')
                                                ->where('order_vendors.sellerid','=',$seller_id)
                                                ->orderBy('order_vendors.created_at','desc')
                                                ->paginate(6);
        $order_query                            =   DB::table('orders')->select('orders.id','orders.order_id','orders.seller_id','users.name as username','orders.order_status_id',
                                                'order_statuses.name as statusname','orders.tax','orders.shipping_charge','orders.payment_status',
                                                'orders.discount','orders.grand_total','orders.payment_type','orders.order_type','orders.created_at')
                                                ->join('users','orders.user_id','=','users.id')
                                                ->join('order_statuses','orders.order_status_id','=','order_statuses.id')
                                                ->latest('orders.created_at')
                                                ->where('orders.seller_id',$seller_id)->paginate(25);
               // $dashboard['order_vendors']         =   [];
        return view('dashboard.seller.home',compact('dashboard'));
    }

    public function getforgetpassword(){
        return view('dashboard.seller.forgetpass');
    }

    public function ForgetPassword(Request $request){
        try {
            $request->validate([
                'email'=>'required|email|exists:sellers,selleremail',
             ],[
                 'email.exists'=>'This email is not exists.'
             ]);
            $user_details   =  Seller::where('selleremail', $request->email)
                                ->where('approval','=','1')->first();
            if(!isset($user_details)){
                throw new Exception("Your account is not approved yet. Please Contact Admin");
            }

            // $user_details   =   seller::where('email',$request->email)->first();
            $mail           =   $request->email;
            $name           =   $user_details->seller_full_name_buss;
            $usertype       =   "Seller";

            if($user_details->is_active != '1'){
                throw new Exception("Account is Inactive. Please Contact Admin");
            }
            if($user_details->forget_pass_token == '1') {
                throw new Exception("Mail Already Sent. Please Check.");
            }
            // dd("am i coming here");
            $SendForgetMail =   $this->SendForgetMail($mail,$name,$usertype);
            if($SendForgetMail['status'] == true) {
                $result     =   ['status' => true, 'message'=>"Mail Sent, Please Check."];
            } else {
                throw new Exception($SendForgetMail['message']);
            }
        } catch (\Exception $e) {
            $result =   ['status' => false, 'message'=>$e->getMessage()];
        }
        if($result['status'] == true)
        {
            return redirect()->route('seller.login')->with('success',$result['message']);
        }
        else
        {
            return redirect()->back()->with('fail',$result['message']);
        }
        return response()->json($result);
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
                $email          =   auth()->guard('seller')->user()->selleremail;
                $password       =   Hash::make(request()->password);
                $VerifiedUser   =   Seller::where('selleremail', $email)
                                    ->where('is_active','=','1')->first();
                if(!$VerifiedUser){
                    throw new Exception("Please Check Email You Provided");
                }
                if(Hash::check(request()->old_password, $VerifiedUser->password)){
                    Seller::where('selleremail',$email)->update(['password'=>$password,'updated_at'=>date('Y-m-d H:i:s')]);
                    $result     =   ['status'=>true, 'message'=>"Password Updated Succesfully"];
                }else{
                    throw new Exception("Please Check the Old Password");
                }
            }
        } catch (\Exception $e) {
            $result =   ['status'=>false, 'message'=>$e->getMessage()];
        }
        if($result['status'] == true){
            Auth::guard('seller')->logout();
            return redirect('/');
        }
        else{
            return back()->with('error',$result['message']);
        }
    }

    function logout()
    {
        Auth::guard('seller')->logout();
        return redirect('/');
    }

}
