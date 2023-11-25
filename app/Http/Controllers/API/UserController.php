<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Seller;
use App\Models\User;
use App\Traits\AuthenticateTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use AuthenticateTrait;

    public function register(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(),[
                'name' => 'required|max:55',
                'email' => 'email|required|unique:users',
                'password'=>'required|confirmed|min:5|max:30',
                'phone'=> 'required'
            ]);

            if($validator->fails())
            {
                $result = ['status'=>false,'message'=> implode( ", ",$validator->errors()->all())];
            }
            else
            {
                $input              = $request->all();
                $input['password']  = Hash::make($request->password);
                $input['is_active'] = '1';

                $customer           = User::create($input);
                if(!$customer){
                    throw new Exception("Something Went Wrong. Try Again Later");
                }


                $data   = ['to'=>$request->email, 'name'=>$request->name,'mail'=>encrypt($request->email)];

                Mail::send('emails.confirm_mail', ["data"=>$data], function($message) use($data) {
                    $message->to($data["to"]);
                    $message->from(env('MAIL_FROM_ADDRESS'), env('APP_NAME'));
                    $message->subject('Email Verification');
                });

                // $accessToken        = $customer->createToken('authToken')->accessToken;

                $result = ['status'=>true, 'message'=>'Success! An e-mail with a verification link has been sent!'];
            }

        }
        catch (\Exception $e)
        {
            $result = ['status'=>false,'message'=> $e->getMessage()];
        }
        return response()->json($result);
    }

    public function VerifyEmail(){
        try {
            $verified_email = decrypt(request()->email);
            $check_email    = User::where('email',$verified_email)->first();
            if(!$check_email){
                throw new Exception("Something Went Wrong. Register Again to Continue");
            }
            if($check_email->email_verified_at != null){
                throw new Exception("Email already Verified. Please Login to Continue");
            }
            $ConfirmVerification    = User::where('email',$verified_email)->update(['email_verified_at'=>date('Y-m-d H:i:s')]);
            if(!$ConfirmVerification){
                throw new Exception("Something Went Wrong in Verifing Email. Please Click Link Again");
            }
            $result = ['status'=>true, 'message'=>"Email Verified Successfully. Please Login to Continue"];
        } catch (\Exception $e) {
            $result = ['status'=>false,'message'=>$e->getMessage()];
        }

        return view('emails.Verification',compact('result'));
    }

    public function login(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(),[
                'email' => 'email|required|exists:users',
                'password' => 'required'
            ]);
            if($validator->fails())
            {
                $result = ['status'=>false,'message'=> implode( ", ",$validator->errors()->all())];
            }
            else
            {
                $VerifiedUser =  User::where('email', $request->email)->whereNotNull('email_verified_at')->exists();
                if(!$VerifiedUser) {
                    throw new Exception("Please Comfirm your Email to Login");
                }

                $userDetails =  User::where('email', $request->email)->where('is_active','=','1')->first();
                if(!$userDetails) {
                    throw new Exception("Something Went Wrong Try Again Later");
                }

                if(Hash::check($request->password, $userDetails->password)) {
                    $accessToken    =   $userDetails->createToken('authToken')->accessToken;
                    $userdata       =   $this->UsertDetail($userDetails->id);
                    $result         = ['status'=>true,'customer' =>  $userdata, 'access_token' => $accessToken, 'message'=>'Success! Logged in!'];
                } else {
                    $result         = ['status'=>false, 'message'=>'Incorrect Password'];
                }
            }
        }
        catch (\Exception $e)
        {
            $result = ['status'=>false,'message'=> $e->getMessage()];
        }
        return response()->json($result);
    }

    public function ForgetPassword()
    {
        try {
            if(request()->has('email') && request()->email != ''){
                $check_user = User::where("email",request()->email)->whereNotNull('email_verified_at')->where('is_active','=','1')->first();
                if(!$check_user){
                    throw new Exception("Please Check the Email You Entered");
                }
                if($check_user->forget_pass_token == '1'){
                    throw new Exception("Mail Already Sent. Please Check Your Mail");
                }
                $name           =   $check_user->name;
                $mail           =   request()->email;
                $usertype       =   "User";
                $SendForgetMail =   $this->SendForgetMail($mail,$name,$usertype);
               if($SendForgetMail['status'] == true){
                    $result =   ['status' => true, 'message'=>" Success! An e-mail with a reset link has been sent!"];
               }else{
                throw new Exception($SendForgetMail['message']);
               }
            }else{
                throw new Exception("Email Is Required");
            }
        } catch (\Exception $e) {
            $result = ['status'=>false, 'message'=>$e->getMessage()];
        }
        return response()->json($result);
    }

    public function ResetEmail(){
        $mail           =   decrypt(request()->email);
        $token          =   decrypt(request()->token);
        $usertype       =   decrypt(request()->usertype);
        $result         =   $this->ResetPasswordLink($token, $mail, $usertype);

        return view('emails.ResetPassword',compact('result'));
    }

    public function UpdatePassword(){
        try {
            $email      =   request()->mail;
            $usertype   =   request()->usertype;
            $password   =   request()->password;
            $conf_pass  =   request()->ConfirmPassword;

            // dd($usertype);
            if($password != $conf_pass){
                throw new Exception("Passwords didn't Match");
            }

            if($usertype    ==  "User"){
                $VerifiedUser   =   User::where('email', $email)
                                    ->whereNotNull('email_verified_at')
                                    ->where('is_active','=','1')->first();
            } else if($usertype == "Admin") {
                $VerifiedUser   =   Admin::where('email', $email)
                                    ->where('is_active','=','1')->first();
            } else if($usertype == "Seller"){
                $VerifiedUser   =   Seller::where('selleremail', $email)->where('approval','1')
                                    ->where('is_active','=','1')->whereNull('deleted_at')->first();
            }

            if(!$VerifiedUser){
                throw new Exception("Something Went Wrong.");
            }
            if($VerifiedUser->forget_pass_token == 1){
                $updatedata     = ['password'=>Hash::make($password),'forget_pass_token'=>'2'];
                if($usertype    ==  "User"){
                    $UpdatePassword =   User::where('email',$email)->update($updatedata);
                } else if($usertype == "Admin") {
                    $UpdatePassword =   Admin::where('email',$email)->update($updatedata);
                } else if($usertype == "Seller"){
                    $UpdatePassword =   Seller::where('selleremail',$email)->update($updatedata);
                }

                if(!$UpdatePassword){
                    throw new Exception("Something Went Wrong. Try Again Later");
                }
                $result     =   ['status'=>true, 'message'=>"Success! Password changed!"];
            } else {
                throw new Exception('Reset Link Already Used / Expired');
            }
        } catch (\Exception $e) {
            $result =   ['status'=>false, 'message'=>$e->getMessage()];
        }
        if($result['status'] == true){
            return redirect()->route('webpage');
        }
        else{
            return redirect()->back()->with('error',$result['message']);
        }
    }

    public function ChangePassword(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(),[
                'old_password'=>'required',
                'password' => 'required|confirmed',
            ]);

            if($validator->fails())
            {
                $result = ['status'=>false,'message'=> implode( ", ",$validator->errors()->all())];
            }
            else
            {
                $email          =   auth()->guard('api')->user()->email;
                $password       =   Hash::make($request->password);
                $VerifiedUser   =   User::where('email', $email)
                                    ->whereNotNull('email_verified_at')
                                    ->where('is_active','=','1')->first();
                if(!$VerifiedUser){
                    throw new Exception("Please Check Email You Provided");
                }
                if(Hash::check($request->old_password, $VerifiedUser->password)){
                    User::where('email',$email)->update(['password'=>$password,'updated_at'=>date('Y-m-d H:i:s')]);
                    $result     =   ['status'=>true, 'message'=>"Password Updated Successfully"];
                }else{
                    throw new Exception("Please Check the Old Password");
                }
            }
        } catch (\Exception $e) {
            $result =   ['status'=>false, 'message'=>$e->getMessage()];
        }
        return response()->json($result);
    }

    public function EditProfile()
    {
        try
        {
            $id         =   auth()->guard('api')->user()->id;
            if(!$id){
                throw new Exception("Please Login To Continue");
            }
            $VerifiedUser   =   User::where('id', $id)
                                ->whereNotNull('email_verified_at')
                                ->where('is_active','=','1')->first();
            if(!$VerifiedUser){
                throw new Exception("Please Check ID You Provided");
            }


            $UpdateData = request()->all();

            if(request()->has('profile') && request()->profile != '')
            {
                $profile  =   request()->profile;
                $img_url = "Customers/".$id."-".str_replace(' ','-', strtolower($VerifiedUser->name));
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

            $updateprofile  =   User::where('id', $id)->update($UpdateData);
            if($updateprofile){
                $result     =   ['status'=>true, 'message'=>"Success! Profile updated!"];
            }else{
                throw new Exception("Something Went Wrong in Updating");
            }
        } catch (\Exception $e) {
            $result =   ['status'=>false, 'message'=>$e->getMessage()];
        }

        return response()->json($result);
    }

    public function UserDet()
    {
        try {
            $id         =   auth()->guard('api')->user()->id;
            if(!$id){
                throw new Exception("Please Login To Continue");
            }
            $user_details   =   $this->UsertDetail($id);
            if(!$user_details)
            {
                throw new Exception("No User Found");
            }

            $result = ['status'=>true, "data"=>$user_details];

        } catch (\Exception $e) {
            $result = ['status'=>false, "message"=>$e->getMessage()];
        }
        return $result;
    }

    function UsertDetail($id)
    {
        $userdata   =   User::where('id',$id)
                        ->whereNotNull('email_verified_at')
                        ->where('is_active','=','1')
                        ->select('id','name','email','phone','about','buss_full_name','profile','created_at')->first();
        if($userdata->profile != null){
            $userdata->profile   =   asset($userdata->profile);
        } else {
            $userdata->profile   =   asset('assets/images/faces/blankuser.png');
        }

        $userdata->joined_date   =   Carbon::parse($userdata->created_at)->toFormattedDateString();
        unset($userdata->created_at);
        return $userdata;
    }

    // public function logout() {
    //     Auth::guard('api')->logout();
    //     return response()->json(['status'=>true, 'mesage'=>"Logged Out Successfully"]);
    // }

    public function logout (Request $request)
    {
        // dd("am i coming here");
        $check_token = Auth::guard('api')->check();
        // dd($check_token);
        $token = $request->user()->token();
        $token->revoke();
        $response = ['status'=>true,'message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }


}
