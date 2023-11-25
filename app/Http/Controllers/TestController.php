<?php

namespace App\Http\Controllers;

use App\Traits\OrderTrait;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class TestController extends Controller
{
    use OrderTrait;
    public function TestMail(){
        try{
            // $data   = ['status'=>false,'message'=>'Approved Successfully','to'=>'kishore@designfort.com', 'name'=>'kishore','mail'=>encrypt('kishore@designfort.com')];

            // Verify Email Data
            // $data   =   ['name'=>'Kishore', 'mail'=>'kishore@designfort.com'];

            // Forget Password
            // $data = [];
            // $data['name'] =  'Kishore';
            // $data['enc_mail'] = 'teklsadkjf@gailc.omc';
            // $data['token'] = 'testasdfjasdlfkashdf';
            // $data['usertype'] = 'user';

            $data = [
                'from'=>env('MAIL_FROM_ADDRESS'),
                'subject'=>"Order Placed #STG220427036",
                'user_name'=>"Kishore",
                'user_email'=>'kishore@designfor.com',
                'order_id' =>"STG220427036",
                'status_id'=>1,
                'status_name'=>'ORDER PLACED',
                'track_msg'=>'Your Order Has been Placed',
                'grand_total'=>'9000 AED',
                'order_type'=>'1',
                'address'=>'#74, 2nd Cross, Razak Garden, Chennai'
            ];
            // $data = [
            //     'from'=>env('MAIL_FROM_ADDRESS'),
            //     'subject'=>$subject,
            //     'user_name'=>$order_det['user_det']->name,
            //     'user_email'=>$order_det['user_det']->email,
            //     'order_id' =>$order_id,
            //     'status_id'=>$status_id,
            //     'status_name'=> strtoupper($order_det['order']->statusname),
            //     'track_msg'=>$trackmsg,
            //     'grand_total'=>$order_det['order']->grand_total,
            //     'order_type'=>$order_type,
            //     'address'=>$address
            // ];
            Mail::send('emails.order_status_mail', ["data"=>$data], function($message) use($data) {
                $message->to("kishore@designfort.com");
                $message->from('info@starling.ae', 'Starling');
                $message->subject($data['subject']);
            });

            echo "Mail Sent sucessfully";
        }
        catch(\Exception $e){
            echo "error : ". $e;
        }
    }

    public function TestOrderMail($order_id){
        $order_det  =   $this->OrderDetails($order_id);
        // dd($order_det['myorderid']);
        $this->sendOrderMail($order_det);
        return "Mail Sent. Please Check";
    }

    public function GetProducts(){
        $productlist    =   DB::table('products')
                            ->leftjoin('sellers','products.seller_id','=','sellers.id')
                            ->leftjoin('product_stocks','products.id','=','product_stocks.product_id')
                            ->leftJoin('categories','products.category_id','=','categories.id')
                            ->where('products.status','1')
                            ->where('sellers.is_active','1')
                            ->where('categories.is_active','1')
                            ->where('product_stocks.price_type','1')
                            ->select('products.id','products.image','products.name','products.category_id',
                            'categories.name as category_name','products.seller_id','sellers.sellername',
                            'product_stocks.product_price','products.is_featured')->get();
            // dd($productlist);
        return view('TestingPage',compact('productlist'));
    }

    public function PaymentPost()
    {
        try {
            $data = [
                'transaction_amount'=>20,
                'currency'=>"AED",
                'customer_address'=>"Address",
                'customer_city'=>'Dubai',
                'billing_country'=>'ARE',
                'billing_state'=>'Dubai',
                'billing_postal_code'=>'000000',
                'customer_name'=>'Test',
                'customer_email'=>'test@email.com',
                'customer_mobile'=>'9876543210'
            ];
            $merchant_key   =  "test_$2y$10$X9WG5PnfdjLJ3QQ-KLvMIOgt2yzHyXkVJe.vYlxkg1-ggIeCeE.om";

            $apiURL = 'https://foloosi.com/api/v1/api/initialize-setup';


            $headers = [
                "merchant_key"=> $merchant_key
            ];



            $response = Http::withHeaders($headers)->asForm()->post($apiURL, $data);

            $statusCode = $response->status();
            $responseBody = json_decode($response->getBody(), true);

            $result =   ['status'=>true,'status_code'=>$statusCode,'response'=>$responseBody];
        } catch (\Throwable $th) {
            $result =   ['status'=>false,'message'=>$th->getMessage()];
        }

        return response()->json($result);

        // return response()->json([]);

        // dd($statusCode);
        // dd($responseBody);
        // $ch = curl_init();

        // curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        // curl_setopt($ch, CURLOPT_POST, true);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

        // $response = curl_exec($ch);

        // // dd($response);
        // $curl = curl_init();
        // curl_setopt_array($curl, array(
        //     CURLOPT_URL => "https://foloosi.com/api/v1/api/initialize-setup",
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_ENCODING => "",
        //     CURLOPT_MAXREDIRS => 10,
        //     CURLOPT_TIMEOUT => 30,
        //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //     CURLOPT_CUSTOMREQUEST => "POST",
        //     CURLOPT_POSTFIELDS => "transaction_amount=1&currency=AED&customer_address=Address&customer_city=Dubai&billing_country=ARE&billing_state=Dubai&billing_postal_code=000000&customer_name=Test&customer_email=test%40email.com&customer_mobile=9876543210",
        //     // CURLOPT_POSTFIELDS => ''.$data.'',
        //     CURLOPT_HTTPHEADER => array(
        //         "content-type: application/x-www-form-urlencoded",
        //         'merchant_key: '.$merchant_key.''
        //     ),
        // ));
        // $response = curl_exec($curl);
        // $err = curl_error($curl);
        // curl_close($curl);

        // if ($err) {
        //     dd("am i getting error ????");
        //     echo "cURL Error #:" . $err;
        // } else {
        //     // dd("am i getting passed here");
        //     $responseData = json_decode($response,true);
        //     // $reference_token = $responseData['data']['reference_token'];
        //     dd($responseData);
        // }
        // die;
        // return $response;
    }

    public function NewAPIResponse(){
        try
        {
            $result =   ['status'=>true,'message'=>'Some Content Comes Here'];
        } catch (\Throwable $th) {
            $result =   ['status'=>false,'message'=>$th->getMessage()];
        }

        return response()->json($result);
    }

    public function AWSUploadTest(Request $request){
        // $image  =   request()->image;
        $this->validate($request, ['image' => 'required|image']);
        if($request->hasfile('image'))
         {
            $file = $request->file('image');
            // $name=time().$file->getClientOriginalName();
            $filePath = 'images';
            // $path = $request->file('image')->store(
            //     $filePath, 's3'
            // );
            $path = Storage::disk('s3')->put($filePath, $request->image);
            $uploadedpath = Storage::disk('s3')->url($path);
            // Storage::disk('s3')->put($filePath, file_get_contents($file));
            // Storage::disk('s3')->url($filePath, file_get_contents($file));
            dd($uploadedpath);
            // $path = $request->file('image')->store('images', 's3');
            return back()->with('success','Image Uploaded successfully & Your Image Path is : '.$path);
         }
    }
}
