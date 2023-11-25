<?php
namespace App\Traits;

use App\Models\Admin;
use App\Models\Seller;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

trait AuthenticateTrait{

    // Common Forget Password
    function SendForgetMail($mail,$name,$usertype){
        try {
            $enc_mail       =   encrypt($mail);
            $now            =   strtotime(date("d-m-Y H:i:s"));
            $token          =   encrypt($now);
            $data           =   ['mail'=>$mail,'enc_mail'=>$enc_mail,'token'=>$token, 'name'=>$name, 'usertype'=>encrypt($usertype)];
            if($usertype == 'User'){
                $SendForgetMail =   User::where('email',$mail)->update(['forget_pass_token'=>'1']);
            } else if ($usertype == 'Admin'){
                // $check_existing_mail_time = Admin::where
                $SendForgetMail =   Admin::where('email',$mail)->update(['forget_pass_token'=>'1']);
            } else if($usertype == 'Seller'){
                $SendForgetMail =   Seller::where('selleremail',$mail)->update(['forget_pass_token'=>'1']);
            }

            if($SendForgetMail){
                Mail::send('emails.reset_password',['data'=>$data], function ($message) use($data){
                    $message->from(env('MAIL_FROM_ADDRESS'), env('APP_NAME'));
                    $message->to($data['mail'], $data['name']);
                    $message->subject('Reset Password');
                });

                $result = ['status'=>true, 'message'=>"Password Reset Link Sent to Mail Successfully"];
            } else {
                throw new Exception("Something Went Wrong Please Try Again Later");
            }
        } catch (\Exception $e) {
            $result = ['status'=>false, 'message'=>$e->getMessage()];
        }
        return $result;
    }

    // Common Reset Password Link
    function ResetPasswordLink($token, $mail, $usertype)
    {
        try
        {
            $token_exp_time =   strtotime(date("d-m-Y H:i:s", strtotime('+60 minutes', $token)));
            $now            =   strtotime(date("d-m-Y H:i:s"));
            if($usertype    ==  "User"){
                $check_mail_status = User::where('email',$mail)->first();
            } else if($usertype == "Admin") {
                $check_mail_status = Admin::where('email',$mail)->first();

            } else if($usertype == "Seller") {
                $check_mail_status = Seller::where('selleremail',$mail)->first();
            }
            if($check_mail_status->forget_pass_token == '1'){
                if($now > $token_exp_time) {
                    if($usertype == 'User'){
                        User::where('email',$mail)->update(['forget_pass_token'=>'2']);
                    } else if ($usertype == 'Admin'){
                        Admin::where('email',$mail)->update(['forget_pass_token'=>'2']);
                    } else if($usertype == 'Seller'){
                        Seller::where('selleremail',$mail)->update(['forget_pass_token'=>'2']);
                    }
                    // User::where('email',$mail)->update(['forget_pass_token'=>'2']);
                    throw new Exception("Link Expired");
                }else {
                    $result     =   ['status'=>true, "mail"=> $mail,'usertype'=> $usertype ];
                }
            } else {
                throw new Exception("Link Already Used");
            }

        } catch (\Throwable $th) {
            $result =   ['status'=>false,"message"=>$th->getMessage()];
        }
        return $result;
    }

    // Common Change Password
    // Common Edit Profile

}
