<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class AdminUserController extends Controller
{
    public function AdminList(){
        $adminlist  =    DB::table('admins')->where('is_active',"!=","2")->get();
        // dd($adminlist);
        return view('dashboard.admin.adminuser.multipleadmin',compact('adminlist'));
    }

    public function AddAdminUser()
    {
        $validator = Validator::make(request()->all(),[
            'name' => 'required|max:55',
            'email' => 'email|required|unique:admins',
            'password'=>'required|confirmed|min:5|max:30',
            'phone'=> 'required'
        ]);

        if($validator->fails())
        {
            $result = ['status'=>false,'message'=> implode( ", ",$validator->errors()->all())];
        }
        else
        {

            $input              =   request()->except('_token','password_confirmation');
            $input['password']  =   Hash::make(request()->password);
            $input['is_super']  =   '0';
            $input['is_active'] =   '1';
            $input['created_at']=   date('Y-m-d H:i:s');
            $input['updated_at']=   date('Y-m-d H:i:s');
            // dd($input);
            $add_admin          =   DB::table('admins')->insert($input);
            if($add_admin)
                $result =   ['status'=>true, 'message'=>"New Admin Added Successfully"];
            else
                $result =   ['status'=>false, 'message'=>"Something Went Wrong"];
        }

        if($result['status'] == true){
            return back()->with('success','Action Completed Successfully');
        } else {
            return back()->with('error','Something Went Wrong');
        }
    }

    public function EditAdminPage($id){
        $admin  =   DB::table('admins')->where('id',$id)->first();
        return view('dashboard.admin.adminuser.edit_admin',compact('admin'));
    }

    public function UpdateAdminProfile(){
        try
        {
            $admin_id       =   request()->admin_id;
            $UpdateData     =   request()->except('_token','admin_id');
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

    public function adminstatus(){
        $dt 			= new \DateTime();
		$datetime		= $dt->format('Y-m-d H:i:s');
        $status         =   request()->is_active;
        $update_data    = [
                            'is_active'     => $status,
                            'updated_at'    => $datetime
                          ];

        $approve        = Admin::where('id',request()->admin_id)->update($update_data);
        if($approve) {
            if($status == '1') {
                $statusname =   'Activated';
            } else if($status   =   '0') {
                $statusname =   'InActivated';
            } else {
                $statusname =   'Deleted';
            }
            return back()->with('success', $statusname.' Successfully');
        }
        else {
            return back()->with('error','Something Went Wrong');
        }
    }
}
