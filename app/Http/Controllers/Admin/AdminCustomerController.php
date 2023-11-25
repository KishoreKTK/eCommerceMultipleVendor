<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserAddress;

class AdminCustomerController extends Controller
{
    //
    public function CustomerList(){
        $customer_list  = DB::table('users')->whereNull("users.deleted_at")->paginate(20);
        return view('dashboard.admin.customers.index',compact('customer_list'));
    }


    public function CustomerDetails($id)
    {
        User::findorfail($id);
        try {
            $customer           =   DB::table('users')->where('id',$id)->first();
            $customer_address   =   UserAddress::select('user_addresses.id','first_name','phone_num','flat_no',
                                    'address','uae_city_emirates.city as area','user_addresses.city','country','default_addr','created_at')
                                    ->leftJoin('uae_city_emirates','uae_city_emirates.id','user_addresses.city')
                                    ->where("user_id",$id)->get();
            //                          DB::table('user_addresses')->where('user_id',$id)
            //                          ->select('user_addresses.*','uae_city_emirates.id as city_id')
            //                          ->leftJoin('uae_city_emirates','uae_city_emirates.id',
            //                          'user_addresses.city')->get();
            // dd($customer_address);
            $saved_products     =   DB::table('user_saved_products')->where('user_id',$id)
                                    ->select('products.id','products.image','products.name',
                                        'product_stocks.product_price as price')
                                    ->join('products','products.id','=','user_saved_products.product_id')
                                    ->join('product_stocks','product_stocks.product_id','=','products.id')
                                    ->get();

            $fav_shops          =   DB::table('user_fav_shops')
                                    ->select('sellers.id as shop_id','sellers.seller_full_name_buss as shopname',
                                        'sellers.sellerarea','sellers.sellerprofile as profile', 'sellers.pickup','sellers.delivery',
                                        'sellers.seller_city as seller_city_id','sellers.city_name as seller_city_name',
                                        'sellers.emirates as emirate_id','uce.city as emirates','sellers.seller_trade_exp_dt')
                                    ->leftjoin(DB::raw('(SELECT s.*, scty.`city` as city_name
                                    FROM sellers AS s
                                    JOIN uae_city_emirates AS scty
                                    ON s.seller_city = scty.id
                                    WHERE s.`deleted_at` IS NULL
                                        ) AS sellers'),
                                    function($join)
                                    {
                                        $join->on('user_fav_shops.shop_id', '=', 'sellers.id');
                                    })
                                    ->leftjoin( 'uae_city_emirates as uce', 'uce.id', 'sellers.emirates')
                                    ->where('sellers.approval','=','1')
                                    ->where('sellers.is_active','=','1')
                                    ->whereNull('sellers.deleted_at')
                                    ->where('user_id',$id)
                                    ->groupBy('shop_id')->get();
            $my_orders  =   DB::table('orders')->where('user_id',$id)->get();
            return  view('dashboard.admin.customers.customerdet',
                    compact('customer','customer_address','saved_products','fav_shops'));
        } catch (\Exception $e) {
            return back()->with('error',$e->getMessage());
        }
    }


    // Activate / Inactivate Customer Action
    public function ChangeUserStatus(Request $request){
        $dt 			= new \DateTime();
        $datetime		= $dt->format('Y-m-d H:i:s');
        $update_data    = [
                            'is_active'     => $request->active_status,
                            'updated_at'    => $datetime
                            ];
                        //   print("<pre>");print_r($request->all());die;
        $approve        = DB::table('users')->where('id',$request->userid)->update($update_data);
        if($approve){
            return back()->with('success','Action Completed Successfully');
        }
        else{
            return back()->with('error','Something Went Wrong');
        }
    }

}
