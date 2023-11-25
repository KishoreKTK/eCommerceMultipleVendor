<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserAddress;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserAddressController extends Controller
{
    public function index(Request $request)
    {
    	$rules=[
    	 	"id"=>"exists:user_address,id,deleted_at,NULL",
            ];
        $msg=[
            "rating.required"=>"Rating is required"
             ];
        $user_id=auth()->guard('api')->user()->id;

        $address_data = $address=UserAddress::select('user_addresses.id','first_name','phone_num','flat_no',
                        'address','uae_city_emirates.city as city','user_addresses.city as city_id','country','default_addr','created_at')
                        ->leftJoin('uae_city_emirates','uae_city_emirates.id','user_addresses.city')
                        ->where("user_id",$user_id);
        if($request->id)
        {
        	$id=$request->id;
        	$address    =   $address_data->where("id",$id)->first();
        }
        else
        {
        	$address    =   $address_data->get();
        }

        if(isset($address))
        {
        	$return['status']=true;
            $return['address']=$address;
            $return['msg']="Your addresses listed successfully";
        }
        else
        {
            $return['status']=false;
            $return['msg']="Sorry no records found ";
        }
        return $return;
    }

    public function add(Request $request)
    {
        $rules=[
                "first_name"=>"required",
                "flatno"=>"required",
                "streetaddress"=>"required",
                "phone"=>"required",
                "country"=>"required"
            ];
        $msg=[
                "streetaddress.required"=>"Address is required"
            ];


        $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
            $return = ['status'=>false,'msg'=> implode( ", ",$validator->errors()->all())];
        }
        else
        {
	        $user_id       =    auth()->guard('api')->user()->id;
            $first_name    =    $request->first_name;
            $phone         =    $request->phone;
	        $address       =    $request->streetaddress;
            $country       =    $request->country;
            $city          =    $request->has('area')?$request->area:null;
            $default_addr  =    $request->has('default_addr')?$request->default_addr:'0';
            $billing_addr  =    $request->has('bill_addr')?$request->bill_addr:'0';
			$time          =    Carbon::now();

            $insert_array  =    [
                                    'address'=>$address,
                                    'user_id'=>$user_id,
                                    'phone_num'=>$phone,
                                    'default_addr'=>$default_addr ,
                                    'billing_addr'=>$billing_addr,
                                    'city'=>$city,
                                    'country'=>$country,
                                    'flat_no'=>request()->flatno,
                                    'first_name'=>$first_name,
                                    'created_at'=> $time,
                                    "updated_at"=>$time
                                ];
            if($request->default_addr== '1')
            {
                $check     =    UserAddress::where("user_id",$user_id)->where('default_addr','1')
                                ->select('id','default_addr')->get();
                if(count($check) > 0)
                {
                    foreach($check as $c)
                    {
                        UserAddress::where("id",$c->id)->update(["default_addr"=>'0',"updated_at"=>$time]);
                    }
                }
                $add_address    =   UserAddress::insertGetId($insert_array);
            }
            else{
                $add_address    =   UserAddress::insertGetId($insert_array);
            }


		    if($add_address)
	        {
                $c_address=UserAddress::select('user_addresses.id','first_name','phone_num','flat_no',
                'address','uae_city_emirates.city as city','user_addresses.city as city_id','country','default_addr','created_at')->where('user_addresses.id',$add_address)
                ->leftJoin('uae_city_emirates','uae_city_emirates.id','user_addresses.city')->get();

                $return['status']  =       true;
	        	$return['msg']    =       "Your address Added successfully";
                $return['address']=       $c_address;
	        }
	        else
	        {
	        	$return['status']   = false;
	            $return['msg']     = "Sorry status occured";
	        }
	    }
	    return $return;
    }

	public function update(Request $request)
	{
        $rules=[
            "id"=>"required|exists:user_addresses,id",
            "first_name"=>"required",
            "flatno"=>"required",
            "streetaddress"=>"required",
            "phone"=>"required",
			"country"=>"required"
            ];
        $msg=[
            "streetaddress.required"=>"Address is required"
        ];


        $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
            $return = ['status'=>false,'msg'=> implode( ", ",$validator->errors()->all())];
        }
        else
        {
            $id             =   $request->id;
            $user_id        =   auth()->guard('api')->user()->id;
            $user_addres    =   $request->streetaddress;
            $time           =   Carbon::now();
            $first_name     =   $request->first_name;
            $phone          =   $request->phone;
            $country        =    $request->country;
            $city           =    $request->has('area')?$request->area:null;
            $default_addr   =    $request->has('default_addr')?$request->default_addr:'0';
            $billing_addr   =    $request->has('bill_addr')?$request->bill_addr:'0';
           // $default  = $request->default_addr;

           $update_address_arr =    [
                                        'address'=>$user_addres,
                                        'user_id'=>$user_id,
                                        'phone_num'=>$phone,
                                        'default_addr'=>$default_addr ,
                                        'billing_addr'=>$billing_addr,
                                        'city'=>$city,
                                        'flat_no'=>request()->flatno,
                                        'country'=>$country,
                                        'first_name'=>$first_name,
                                        'created_at'=> $time,
                                        "updated_at"=>$time
                                    ];
           if($request->default_addr=='1')
            {

            $address    =   UserAddress::select('user_addresses.id','first_name','phone_num','flat_no',
                            'address','uae_city_emirates.city as city','user_addresses.city as city_id','country','default_addr','created_at')
                            ->where('user_addresses.id',$id)
                            ->leftJoin('uae_city_emirates','uae_city_emirates.id','user_addresses.city')
                            ->where("user_id",$user_id)->get();


                $address        =   UserAddress::where('user_id',$user_id)->where('default_addr','1')->get();
                if(isset($address) && count($address)>0)
                {
                    $update_address =   UserAddress::where("user_id",$user_id)
                                ->update(["default_addr"=>'0',"updated_at"=>$time]);
                }
                $update_address =   UserAddress::where("id",$id)
                                    ->update($update_address_arr);
            }
            else
            {
                $update_address =   UserAddress::where("id",$id)
                                    ->update($update_address_arr);

            }
        //    $c_address=UserAddress::select('id','first_name','phone_num','flat_no',
        //    'address','city as area','country','default_addr','created_at')->where('id',$id)->get();
           if($update_address)
           {
            $c_address=UserAddress::select('user_addresses.id','first_name','phone_num','flat_no',
                        'address','uae_city_emirates.city as city','user_addresses.city as city_id','country','default_addr','created_at')
                        ->where('user_addresses.id',$id)
                        ->leftJoin('uae_city_emirates','uae_city_emirates.id','user_addresses.city')
                        ->where('user_addresses.id',$id)->get();


               $return['status']    =   true;
               $return['msg']       =   "user address has updated sucessfully";
               $return['address']   =   $c_address;
           }
           else
           {
               $return['status']    =   false;
               $return['msg']       =   "Sorry status occured";
           }
        }
        return $return;
	}

    public function UpdateDefaultAddress($id){
        $address_id = UserAddress::find($id);
        if ($address_id == null || $address_id == '') {
            $return['status']=false;
            $return['msg']="Sorry no records found ";
        } else {
            $time           =   Carbon::now();
            $api_token      =   request()->header('User-Token');
	        $user_id        =   auth()->guard('api')->user()->id;
            $address        =   UserAddress::select('id','first_name','phone_num','flat_no',
                                'address','city as area','country','default_addr','created_at')->where('user_id',$user_id)->where('default_addr','1')->get();
            if(isset($address) && count($address)>0)
            {
                UserAddress::where("user_id",$user_id)
                            ->update(["default_addr"=>'0',"updated_at"=>$time]);
            }

            UserAddress::where("id",$id)
                            ->update(["default_addr"=>'1',"updated_at"=>$time]);

            $address=UserAddress::select('user_addresses.id','first_name','phone_num','flat_no',
                            'address','uae_city_emirates.city as city','user_addresses.city as city_id','country','default_addr','created_at')
                            ->where('user_addresses.id',$id)
                            ->leftJoin('uae_city_emirates','uae_city_emirates.id','user_addresses.city')
                            ->where("user_id",$user_id)->get();


            // $address=UserAddress::select('id','first_name','phone_num','flat_no',
            // 'address','city as area','country','default_addr','created_at')->where("user_id",$user_id)->get();
            if(isset($address))
            {
                $return['status']=true;
                $return['address']=$address;
                $return['msg']="Your addresses listed successfully";
            }
            else
            {
                $return['status']=false;
                $return['msg']="Sorry no records found ";
            }
            return $return;
        }
    }


    public function delete(Request $request)
    {
    	 $rules=[
    	 	"id"=>"required|exists:user_addresses,id",
            ];
        $msg=[
            "id.required"=>"ID is required"
             ];
        $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
        	$return['status']=false;
		    $return['msg']=$validator->errors()->all();
        }
        else
        {
	        $user_id=auth()->guard('api')->user()->id;
	        $id=$request->id;
	        $delete=UserAddress::where("id",$id)->where("user_id",$user_id)->delete();
	        if($delete)
	        {
	        	$return['status']=true;
	        	$return['msg']="Your address deleted successfully";
	        }
	        else
	        {
	        	$return['statys']=false;
	            $return['msg']="Sorry status occured";
	        }
	    }
	    return $return;
    }

    public function uae_city_api(){
        try {
            $city_lists =   DB::table('uae_city_emirates')->select('id','city')
                            ->get();
            $result = ['status'=>true,'data'=>$city_lists,'message'=>'Cities Listed Successfully'];
        } catch (\Exception $th) {
            $result = ['status'=>true,'message'=>$th->getMessage()];
        }
        return response()->json($result);
    }

    public function CheckDeliveryCity(){
        try{
            // User Id, User Address id, Seller Id
            $rules=[
                "address_id"=>"required",
                "seller_id"=>"required"
            ];

            $msg=[
                "streetaddress.required"=>"Address is required"
            ];


            $validator=Validator::make(request()->all(), $rules, $msg);

            if($validator->fails())
            {
                throw new Exception(implode( ", ",$validator->errors()->all()));
            }

            $user_id        =   auth()->guard('api')->user()->id;
            $address_id     =   request()->address_id;
            $seller_id      =   request()->seller_id;

            $address        =   DB::table('user_addresses')->where('id',$address_id)->first();
            $user_city_id   =   $address->city;

            $seller         =   DB::table('sellers')->where('id',$seller_id)->first();
            $seller_city_id =   $seller->seller_city;

            $get_delivery_det   =   DB::table('seller_shipping_details')->where('seller_id',$seller_id)
                                    ->where('from_city',$seller_city_id)->where('to_city',$user_city_id)
                                    ->first();
            if(!$get_delivery_det){
                throw new Exception("No Delivery Available for your City");
            }

            $result     =   ['status'=>true, 'data'=>['delivery_available'=>true,'develivery_charges'=>$get_delivery_det->fees] , 'message'=>'You Can Proceed Ordering'];
        }catch(\Exception $e){
            $result     =   ['status'=>false, 'message'=>$e->getMessage()];
        }

        return response()->json($result);
    }
}
