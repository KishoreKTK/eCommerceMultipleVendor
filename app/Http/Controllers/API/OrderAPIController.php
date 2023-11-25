<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Cart;
use App\Models\UserAddress;
use App\Models\OrderQuantity;
use App\Models\BookingAddress;
use App\Models\Order;
use App\Models\Seller;
use App\Traits\OrderTrait;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Exception;

class OrderAPIController extends Controller
{
    use OrderTrait;
    //
    public function ProceedtoCheckOut()
    {
        try
        {
            $user_id        =   auth()->guard('api')->user()->id;
            $check_address  =   DB::table('user_addresses')->where('user_id',$user_id)->count();
            if($check_address == 0){
                throw new Exception("Please Add Address to Continue Your Order");
            }
            $user_details   =   DB::table('users')->select('users.id','users.name','users.email','users.phone','users.profile',
                                'users.about','ua_tbl.id as address_id','flat_no','address as street_address',
                                'ua_tbl.city as city_id','ua_tbl.cityname','country','default_addr')
                                ->where('users.id',$user_id)->where('default_addr','1')
                                ->leftjoin(DB::raw('(SELECT
                                        ua.*, uce.city as cityname
                                        FROM user_addresses AS ua
                                        LEFT JOIN uae_city_emirates AS uce
                                        ON uce.id = ua.city
                                ) AS ua_tbl'),
                                function($join)
                                {
                                    $join->on('users.id', '=', 'ua_tbl.user_id');
                                })
                                // ->leftJoin('user_addresses','user_addresses.user_id','users.id')
                                // ->leftjoin( 'uae_city_emirates as uce', 'uce.id', 'user_addresses.city')
                                ->first();


            $cartlist       =   Cart::where('user_id',$user_id)
                                ->leftjoin('products','products.id','carts.product_id')
                                ->leftjoin('product_stocks',
                                    'product_stocks.product_entry','carts.product_entry_id')
                                ->select('products.id','products.image','products.name',
                                    'products.seller_id', 'product_stocks.quantities as avaialble_qty',
                                    'products.description', 'product_stocks.product_price',
                                    'product_stocks.product_entry','carts.options',
                                    'product_stocks.min_order_qty','carts.product_qty as selected_qty')
                                ->get();
            if(count($cartlist) > 0)
            {
                $total_price_amount     =   0;
                $seller_ids             =   [];

                foreach($cartlist as $cart){
                    array_push($seller_ids,$cart->seller_id);
                    $seller_det   =   Seller::where('id',$cart->seller_id)->first();
                    if($seller_det){
                        $cart->seller_name = $seller_det->seller_full_name_buss;
                    } else {
                        $cart->seller_name = null;
                    }

                    $cart->total_price   =   $cart->selected_qty * $cart->product_price;
                    $cart->options      =   json_decode($cart->options);

                    $total_price_amount  =   $total_price_amount + $cart->total_price;
                }
                $seller         =   array_unique($seller_ids);
                $seller_id      =   $seller[0];
                $seller_det     =   DB::table('sellers')->select('sellers.id as shop_id',
                                    'sellers.sellername as shopname',
                                    'sellers.seller_full_name_buss as shopname',
                                    'sellers.sellerprofile as profile','sellers.sellerarea',
                                    'sellers.seller_city as seller_city_id','sellers.pickup',
                                    'sellers.cash_on_delivery','sellers.delivery',
                                    'sellers.tax_handle','uae_city_emirates.city as seller_city_name',
                                    'sellers.emirates as emirate_id', 'sellers.seller_trade_exp_dt',
                                    'uce.city as emirates')
                                    ->Join('uae_city_emirates','uae_city_emirates.id','sellers.seller_city')
                                    ->join( 'uae_city_emirates as uce', 'uce.id', 'sellers.emirates')
                                    ->where('sellers.approval','=','1')
                                    ->whereNull('sellers.deleted_at')
                                    ->where('sellers.is_active','=','1')
                                    ->where('sellers.id',$seller_id)->first();

                $order_details['user_det']          =   $user_details;
                $order_details['products']          =   $cartlist;
                $order_details['seller_det']        =   $seller_det;
                $order_details['item_cost']         =   $total_price_amount;

                if($seller_det->delivery == 1)
                {
                    $shipping_charges               =   DB::table('seller_shipping_details')
                                                        ->where('seller_id',$seller_id)
                                                        ->where('from_city',$seller_det->seller_city_id)
                                                        ->where('to_city',$user_details->city_id)
                                                        ->first();
                    if($shipping_charges)
                    {
                        $shiping_available              =   true;
                        $shipping_fees                  =   $shipping_charges->fees;
                    } else{
                        $shiping_available              =   false;
                        $shipping_fees                  =   0;
                    }
                } else {
                    $shipping_fees                      =   0;
                    $shiping_available                  =   false;
                }
                $order_details['delivery_available']    =   $shiping_available;
                $order_details['develivery_charges']    =   $shipping_fees;

                if($seller_det->tax_handle == 1)
                {
                    $tax_percentage_amt                     =   (5 / 100) * $total_price_amount;
                    $order_details['tax_percentage']        =   "5 %";
                    $order_details['tax_amount']            =   $tax_percentage_amt;
                } else {
                    $tax_percentage_amt                     =   (0 / 100) * $total_price_amount;
                    $order_details['tax_percentage']        =   "0 %";
                    $order_details['tax_amount']            =   floatval($tax_percentage_amt);
                }

                $processing_fees_percentage                =   "1 %";
                $processing_fees                            =   (1 / 100) * $total_price_amount;
                $order_details['processing_fees']           =   $processing_fees;

                $sub_total                                  =   $total_price_amount + $tax_percentage_amt + $processing_fees;
                $order_details['sub_total']                 =   $sub_total;

                $folossi_transaction_fees                   =   round((2.99 / 100) * $sub_total, 2);
                $folossi_fixed_fee                          =   0.90;
                $folossi_vat                                =   (5/100)*($folossi_transaction_fees + $folossi_fixed_fee);
                $transaction_fees                           =   $folossi_transaction_fees + $folossi_fixed_fee + $folossi_vat;
                $order_details['transaction_fees']          =   number_format((float)$transaction_fees, 2, '.', '');

                $total_amount                               =   $sub_total + $transaction_fees;
                $order_details['total_amount']              =   number_format((float)$total_amount, 2, '.', '');

                $order_details['FOLOOSI_MERCHANT_KEY']      =   env('FOLOOSI_MERCHANT_KEY');
                $result     =   [
                                    'status'=>true,
                                    'count'=>count($cartlist),
                                    'data'=> $order_details,
                                    'message'=>'Check Out Details successfully'
                                ];
            }
            else
            {
                throw new Exception("No Items in Cart. Please Add Items to Continue.");
            }
        }
        catch (\Exception $e)
        {
            $result = ['status'=>false,'message'=> $e->getMessage()];
        }
        return response()->json($result);
    }

    public function PlaceOrder(Request $request)
    {
        try
        {
            $validator  =   Validator::make($request->all(),[
                                "products"              => "required|array",
                                "products.*.product_id" => "required",
                                "products.*.product_qty"=> "required",
                                "address_id"            => "required|exists:user_addresses,id",
                                "order_type"            => "required",
                                "payment_type"          => "required"
                            ]);
            if($validator->fails())
            {
                $errors     = implode( ", ",$validator->errors()->all());
                // json_encode($validator->errors()->all())
                throw new \Exception($errors);
            }
            else
            {
                $products           =   $request->products;
                $user_id            =   auth()->guard('api')->user()->id;
                $product_qty_price  =  [];
                $product_qty_det    =  [];

                foreach ($products as $key => $product)
                {
                    $check_product =   DB::table('product_stocks')
                                        ->select('product_id as id','product_price as price','quantities')
                                        ->where('product_id',$product['product_id'])
                                        ->where('quantities','>=',$product['product_qty'])
                                        ->where('product_entry','=',$product['product_entry_id'])
                                        ->first();
                    if($check_product)
                    {
                        $product_qty_price[$check_product->id]  = $check_product->price * $product['product_qty'];
                        $product_qty_det[$key]["product_id"]    = $check_product->id;
                        $product_qty_det[$key]["product_price"] = $check_product->price;
                        $product_qty_det[$key]["avail_qty"]     = $check_product->quantities;
                        $product_qty_det[$key]["quantity"]      = $product['product_qty'];
                        $product_qty_det[$key]["total_price"]   = $check_product->price * $product['product_qty'];
                    }
                    else
                    {
                        throw new \Exception("Out of Stock / Please Reduce Quantity");
                    }
                }

                $order_id       =   $this->generateUniqueNumber();
                $address_id     =   $request->address_id;
                $orderdata      =   [
                                        'order_id'=>$order_id,
                                        'user_id'=>$user_id,
                                        'address_id'=>$address_id,
                                        'products'=>$products,
                                        'order_type'=>request()->order_type,
                                        'payment_type'=>request()->payment_type
                                    ];
                $NewOrder       =   $this->InsertNewOrder($orderdata);
                if($NewOrder['status']  == true){
                    $order_type =   request()->order_type;
                    $order_det  =   $this->OrderDetails($order_id);

                    // Clear Cart
                    $cart = Cart::where('user_id',$user_id);

                    if($cart->exists())
                    {
                        $cart->delete();
                    }

                    $this->sendOrderMail($order_det);

                    // $order_det              =   $this->OrderDetails($order_id);

                    $product_qty_det        =   $order_det['qty_det'];
                    $produc_updated_ids     =   [];
                    foreach($product_qty_det as $key=>$prod){
                        $product_id             =   $prod->prdt_id;
                        $produt_entry_id        =   $prod->produt_entry_id;
                        $product_det            =   DB::table('products')->where('id',$product_id)->first();
                        $remaining_qty          =   $product_det->total_qty - $prod->prod_qty;
                        if($key == 0)
                        {
                            array_push($produc_updated_ids,$product_id);
                            $check_prod_already_updated = false;
                        } else {
                            if (in_array($product_id, $produc_updated_ids)){
                                $check_prod_already_updated = true;
                            } else {
                                $check_prod_already_updated = false;
                            }
                        }
                        if($check_prod_already_updated == false){
                            DB::table('products')->where('id',$product_id)->update(['total_qty'=>$remaining_qty,'updated_at'=>date('Y-m-d H:i:s')]);
                            DB::table('product_stocks')->where('product_id',$product_id)->where('price_type','1')->update(['quantities'=>$remaining_qty,'updated_at'=>date('Y-m-d H:i:s')]);
                        }
                        $check_entry_id_exists  =   DB::table('product_stocks')->where('product_id',$product_id)->where('price_type','2')
                                                    ->where('product_entry',$produt_entry_id)->first();

                        if($check_entry_id_exists){
                            DB::table('product_stocks')->where('product_id',$product_id)
                            ->where('product_entry',$produt_entry_id)->update(['quantities'=>$remaining_qty,'updated_at'=>date('Y-m-d H:i:s')]);
                        }
                    }
                    // $this->sendOrderMail($order_det);

                    $result = [
                                'status'=>true,
                                'data'=>[
                                            'order_type'    => $order_type,
                                            'order_details' => $order_det
                                        ],
                                "Message"=>"Thank You For Ordering."];
                }
                else{
                    throw new Exception($NewOrder['message']);
                }
            }
        }
        catch (\Exception $e)
        {
            $result     = ['status'=>false,'message'=> $e->getMessage()];
        }
        return response()->json($result);
    }

    public function UpdateTransactionStatus()
    {
        try
        {
            $order_id               =   request()->order_id;

            $update_data            =   [
                'transaction_id'    =>  request()->transaction_id,
                'payment_status'    =>  '1',
                'updated_at'        =>  date('Y-m-d H:i:s')
            ];
            $update = Order::where('order_id',$order_id)->Update($update_data);
            if(!$update){
                throw new Exception('Something Went Wrong. Try again later');
            }

            $result =   ['status'=>true,'message'=>'Thank You for Ordering'];
        } catch (\Exception $th) {
            $result =   ['statut'=>false,'message'=>$th->getMessage()];
        }
        return response()->json($result);
    }

    Public function ParticularOrderDet($id)
    {
        try{
            $CheckOrderId   = Order::find($id);

            if ($CheckOrderId)
            {
                // Check Weather This Order Belongs to My Order
                $order_user_id          =   $CheckOrderId->user_id;
                $my_user_id             =   auth()->guard('api')->user()->id;
                if($order_user_id       !=  $my_user_id) {
                    throw new Exception("This is not your Order ID");
                }
                $result = ['status'=>true,'data'=> $this->OrderDetails($CheckOrderId->order_id) , "Message"=>"Order Details"];
            } else {
                throw new Exception("Please Check the Order Id");
            }
        }
        catch (\Exception $e)
        {
            $result     = ['status'=>false,'message'=> $e->getMessage()];
        }
        return response()->json($result);
    }

    Public function MyOrders()
    {
        try
        {
            $user_id            =   Auth::guard('api')->user()->id;
            $my_orders          =   $this->CustomerOrders($user_id);

            if($my_orders['status'] == true)
            {
                $result = ['status'=>true, 'data'=>$my_orders['data']];
            }
            else
            {
                throw new Exception($my_orders['message']);
            }
        }
        catch (\Exception $e)
        {
            $result     = ['status'=>false,'message'=> $e->getMessage()];
        }
        return response()->json($result);

    }


}
