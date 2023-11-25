<?php
namespace App\Traits;

use App\Models\Attributes;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderBookingAddress;
use App\Models\OrderPaymentAddress;
use App\Models\OrderShippingAddress;
use App\Models\OrderStatus;
use App\Models\OrderStatusTrack;
use App\Models\OrderVendor;
use App\Models\User;
use App\Models\UserAddress;
use Attribute;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

trait OrderTrait{

    function Orderstuatuslist(){
        $order_statuses = DB::table('order_statuses')->where('id','!=',8)->where('active_status','1')->get();
        return $order_statuses;
    }

    function OrderColorCode($status_id){
        $colorname                  =   '';
        $color_code                 =   [];
        $color_code['info']         =   [1];
        $color_code['success']      =   [3,4,5,6,7];
        $color_code['dark']         =   [8];
        $color_code['danger']       =   [2];
        $color_code['secondary']    =   [9];
        foreach($color_code as $color=>$status_arr){
            if(in_array( $status_id, $status_arr)){
                $colorname  = $color;
            }
        }
        return $colorname;
    }

    function generateUniqueNumber()
    {
        $dt         = new \DateTime();
        $date       = $dt->format('ymd');
        $orderObj   = DB::table("orders")->select('order_id')->latest('id')->first();
        if ($orderObj) {
            $orderNr            =   $orderObj->order_id;
			$removed1char       =   substr($orderNr,9);
            $dateformat         =   str_pad($removed1char + 1, 3, "0", STR_PAD_LEFT);
            $uid                =   $date. $dateformat;
            $generateOrder_nr   =   "STG".$uid;
        } else {
            $generateOrder_nr   =   "STG".$date . str_pad(1, 3, "0", STR_PAD_LEFT);
        }
        return $generateOrder_nr;
	}

    function GetAddressData($orderId,$user_id,$address_id)
    {
        $InsertAddress   =   UserAddress::select('addr_title','first_name','last_name','flat_no',
                                                'phone_num','city','country','address')
                            ->where('user_id',$user_id)->where('id',$address_id)
                            ->first();
        $InsertAddress->order_id    =   $orderId;
        $InsertAddress->created_at  =   date('Y-m-d H:i:s');
        $InsertAddress->updated_at  =   date('Y-m-d H:i:s');

        return $InsertAddress;
    }

    function InsertNewOrder($orderdata)
    {
        try {
            $orderId            =   $orderdata['order_id'];
            $user_id            =   $orderdata['user_id'];
            $products           =   $orderdata['products'];
            $address_id         =   $orderdata['address_id'];
            $order_type         =   $orderdata['order_type'];
            $payment_type       =   $orderdata['payment_type'];
            $ordervendordata    =   [];

            $seller_ids         =   [];

            $user_details   =   DB::table('users')->select('users.id','users.name','users.email','users.phone','users.profile',
                                'users.about','user_addresses.id as address_id',
                                'flat_no','address as street_address',
                                'city','country','default_addr')->where('users.id',$user_id)
                                ->where('user_addresses.id',$address_id)
                                ->leftJoin('user_addresses','user_addresses.user_id','users.id')
                                ->first();


            $total_price_amount     =   0;

            foreach ($products as $key => $product)
            {

                $product_det    =   DB::table('products')->select('products.id as productid',
                                    'sellers.id as sellerid', 'sellers.commission','product_stocks.quantities',
                                    'product_stocks.product_price','product_stocks.product_entry')
                                    ->join('sellers','products.seller_id','=','sellers.id')
                                    ->join('product_stocks','products.id','=','product_stocks.product_id')
                                    ->where('products.id',$product['product_id'])
                                    ->where('product_stocks.product_entry','=',$product['product_entry_id'])
                                    ->first();
                array_push($seller_ids,$product_det->sellerid);


                $ordervendordata[$key]['orderid']           =    $orderId;
                $ordervendordata[$key]['sellerid']          =    $product_det->sellerid;
                $ordervendordata[$key]['productid']         =    $product_det->productid;
                $ordervendordata[$key]['produt_entry_id']   =    $product['product_entry_id'];
                $ordervendordata[$key]['options']           =    json_encode($product['options']);
                $ordervendordata[$key]['prod_qty']          =    $product['product_qty'];
                $ordervendordata[$key]['price_per_unit']    =    $product_det->product_price;
                $ordervendordata[$key]['product_commission']=    0;
                $ordervendordata[$key]['seller_commission'] =    $product_det->commission;
                $ordervendordata[$key]['seller_commission_perc'] =    $product_det->commission;
                $ordervendordata[$key]['seller_tax']        =    0;
                $ordervendordata[$key]['seller_tax_percent']=    0;
                $ordervendordata[$key]['shipping_charges']  =    0;
                $total_amount                               =   $product_det->product_price * $product['product_qty'];
                $ordervendordata[$key]['total_amount']      =   $total_amount;
                $ordervendordata[$key]['orderstatus']       =    '1';
                $ordervendordata[$key]['created_at']        =    date('Y-m-d H:i:s');
                $ordervendordata[$key]['updated_at']        =    date('Y-m-d H:i:s');
                $total_price_amount  =   $total_price_amount + $total_amount;

            }

            // dd($ordervendordata);

            $seller         =   array_unique($seller_ids);
            $seller_id      =   $seller[0];

            $seller_det     =   DB::table('sellers')->select('sellers.id as shop_id','sellers.sellername as shopname',
                                'sellers.seller_full_name_buss as shopname',
                                'sellers.sellerprofile as profile','sellers.sellerarea',
                                'sellers.seller_city as seller_city_id','sellers.pickup','sellers.delivery',
                                'sellers.tax_handle','uae_city_emirates.city as seller_city_name',
                                'sellers.emirates as emirate_id', 'sellers.seller_trade_exp_dt',
                                'uce.city as emirates')
                                ->Join('uae_city_emirates','uae_city_emirates.id','sellers.seller_city')
                                ->join( 'uae_city_emirates as uce', 'uce.id', 'sellers.emirates')
                                ->where('sellers.approval','=','1')
                                ->whereNull('sellers.deleted_at')
                                ->where('sellers.is_active','=','1')
                                ->where('sellers.id',$seller_id)->first();

            // dd($seller_det);
            // Order Type - 1- pickup; 2-deliver;
            if($order_type == '2')
            {
                $payment_status = '0';
                $shipping_charges               =   DB::table('seller_shipping_details')
                                                    ->where('seller_id',$seller_id)
                                                    ->where('from_city',$seller_det->seller_city_id)
                                                    ->where('to_city',$user_details->city)
                                                    ->first();
                if($shipping_charges){
                    $shipping_fees              =   $shipping_charges->fees;
                } else{
                    throw new Exception("Delivery not Available for your Area");
                }
            } else {
                $shipping_fees              =   0;
                $payment_status             =   '0';
            }
            // $delivery_available       =   $shiping_available;
            $develivery_charges          =   $shipping_fees;
            if($seller_det->tax_handle == 1)
            {
                $tax_percentage         =   5;
                $tax_amount             =   (5 / 100) * $total_price_amount;
            } else {
                $tax_percentage         =   0;
                $tax_amount             =   0;
            }
            $processing_fees            =   (1 / 100) * $total_price_amount;
            $commission_amt             =   1;
            $sub_total                  =   $total_price_amount + $tax_amount + $processing_fees + $develivery_charges;

            $folossi_transaction_fees   =   round((2.99 / 100) * $sub_total, 2);
            $folossi_fixed_fee          =   0.90;
            $folossi_vat                =   (5/100)*($folossi_transaction_fees + $folossi_fixed_fee);
            if($payment_type == '1'){
                $transaction_fees       =   $folossi_transaction_fees + $folossi_fixed_fee + $folossi_vat;
            } else {
                $transaction_fees       =   0;
            }
            $total_amount               =   $sub_total + $transaction_fees;

            $grand_total                =   number_format((float)$total_amount, 2, '.', '');


            $insert_data            =   [
                'order_id'          =>  $orderId,
                'user_id'           =>  $user_id,
                'seller_id'         =>  $seller_id,
                'address_id'        =>  $address_id,
                'payment_addr_id'   =>  $address_id,
                'billing_addr_id'   =>  $address_id,
                'order_status_id'   =>  1,
                'discount'          =>  0,
                'promocode'         =>  0,
                'tax'               =>  $tax_amount,
                'tax_percentage'    =>  $tax_percentage,
                'shipping_charge'   =>  $develivery_charges,
                'processing_fee'    =>  $processing_fees,
                'transaction_fee'   =>  number_format((float)$transaction_fees, 2, '.', ''),
                'commission'        =>  $commission_amt,
                'sub_total'         =>  $sub_total,
                'grand_total'       =>  $grand_total,
                'transaction_id'    =>  null,
                'order_type'        =>  $order_type,
                'payment_type'      =>  $payment_type,
                'payment_status'    =>  $payment_status,
                'created_at'        =>  date('Y-m-d H:i:s'),
                'updated_at'        =>  date('Y-m-d H:i:s')
            ];

            $InsertOrder            =   Order::insert($insert_data);
            if(!$InsertOrder){
                throw new Exception("Something Went Wrong");
            }
            $InsertOrderVendor      =   DB::table('order_vendors')->insert($ordervendordata);
            if(!$InsertOrderVendor){
                throw new \Exception("Something Went Wrong in Adding Order Vendors Data");
            }

            $order_vendor_data  =   DB::table('order_vendors')->select('id')->where('orderid',$orderId)->get();

            $OrderStatusData    =   [];
            foreach ($order_vendor_data as $key => $value) {
                $OrderStatusData[$key]['sub_order_id']  =   $orderId;
                $OrderStatusData[$key]['order_status']  =   "1";
                $OrderStatusData[$key]['remarks']       =   "New Order Added";
                $OrderStatusData[$key]['created_at'] = date('Y-m-d H:i:s');
            }

            $InsertOrderStatus  = OrderStatusTrack::insert($OrderStatusData);
            if(!$InsertOrderStatus){
                throw new \Exception("Something Went Wrong in Adding Order Status Track");
            }

            $OrderBookingAddress  =   OrderBookingAddress::insert(json_decode($this->GetAddressData($orderId,$user_id,$address_id), true));
            if(!$OrderBookingAddress){
                throw new \Exception("Something Went Wrong in Adding Bookding Address");
            }

            $OrderPaymentAddress  =   OrderPaymentAddress::insert(json_decode($this->GetAddressData($orderId,$user_id,$address_id), true));
            if(!$OrderPaymentAddress){
                throw new \Exception("Something Went Wrong in Adding Payment Address");
            }

            $OrderShippingAddress  =   OrderShippingAddress::insert(json_decode($this->GetAddressData($orderId,$user_id,$address_id), true));
            if(!$OrderShippingAddress){
                throw new \Exception("Something Went Wrong in Adding Shipping Address");
            }
            $result =   ['status'=>true, 'message'=> 'Order Inserted Successfully'];
        } catch (\Exception $e) {
            $result =   ['status'=>false, 'message'=>$e->getMessage()];
        }
        return $result;
    }

    function OrderDetails($OrderId)
    {
        $Order_Details_Arr  =   [];
        $myorder    =   DB::table('orders')->where('order_id',$OrderId)->first();
        $Order_Details_Arr['myorderid']         =    $myorder->id;
        $order_type_name = ($myorder->order_type == 1) ? 'Delivery' : 'Pickup';
        if($myorder->payment_type == '1')
        {
            $Order_Details_Arr['payment_type']    =   "Card";
        }
        else if($myorder->payment_type == '2')
        {
            $Order_Details_Arr['payment_type']    =   "Cash";
        }
        else
        {
            $Order_Details_Arr['payment_type']    =  "Bank";
        }
        if($myorder->payment_status == '1')
        {
            $Order_Details_Arr['payment_status_name']    =   "Paid";
        }
        else if($myorder->payment_status == '2')
        {
            $Order_Details_Arr['payment_status_name']    =   "Refunded";
        }
        else
        {
            $Order_Details_Arr['payment_status_name']    =  "Pending";
        }

        $Order_Details_Arr['order']    =    DB::table('orders')
                                            ->select('orders.order_id','orders.user_id','orders.payment_status','orders.payment_type',
                                            'orders.order_status_id','orders.shipping_charge','orders.transaction_id',
                                            'orders.tax','tax_percentage','order_statuses.name as statusname',
                                            'orders.processing_fee','orders.transaction_fee','orders.sub_total',
                                            'orders.grand_total','orders.order_type','orders.created_at')
                                            ->join('order_statuses','orders.order_status_id','=','order_statuses.id')
                                            ->where('order_id',$OrderId)->first();

        $Order_Details_Arr['order']->created_at = Carbon::parse($Order_Details_Arr['order']->created_at)->toFormattedDateString();

        $Order_Details_Arr['address']   =   DB::table('order_booking_addresses')
                                            ->select('first_name','phone_num',
                                            'flat_no','country',
                                            'address','order_booking_addresses.city as cityid','uae_city_emirates.city')
                                            ->leftJoin('uae_city_emirates','uae_city_emirates.id',
                                            'order_booking_addresses.city')
                                            ->where('order_id',$OrderId)->first();

        $Order_Details_Arr['user_det']  =   DB::table('users')->select('id','name','email','phone')
                                            ->where('id',$Order_Details_Arr['order']->user_id)
                                            ->first();

        $Order_Details_Arr['qty_det']   =   DB::table('order_vendors')
                                            ->select('order_vendors.id as suborderid',
                                                    'products.id as prdt_id',
                                                    'products.image',
                                                    'order_vendors.produt_entry_id',
                                                    'order_vendors.options',
                                                    'products.name as prod_name',
                                                    'sellers.id as seller_id',
                                                    'sellers.sellername',
                                                    'order_vendors.price_per_unit',
                                                    'order_vendors.prod_qty'
                                                    ,'order_vendors.total_amount',
                                                    'order_vendors.orderstatus as vendoerorderstatusid',
                                                    'order_statuses.name as orderstatusname')
                                            ->join('products','products.id','=','order_vendors.productid')
                                            ->join('sellers','sellers.id','=','order_vendors.sellerid')
                                            ->join('order_statuses','order_vendors.orderstatus','=','order_statuses.id')
                                            ->where('orderid',$OrderId)
                                            ->get();

        $seller_id  = '';
        foreach ($Order_Details_Arr['qty_det'] as $key => $value) {

            $seller_id  =   $Order_Details_Arr['qty_det'][$key]->seller_id;
            $options    =   json_decode($value->options);
            foreach($options as $opt){
                $options_id =   $opt->option_id;
                $attr_name  =   Attributes::where('id',$options_id)->first()->name;
                $opt->attr_name = $attr_name;
            }
            $Order_Details_Arr['qty_det'][$key]->options=   $options;
            $Order_Details_Arr['qty_det'][$key]->image  = asset($value->image);
        }

        $Order_Details_Arr['shop_address']  =   DB::table('sellers')->where('sellers.id',$seller_id)
                                                ->select('sellers.id','seller_full_name_buss as shopname',
                                                'sellername as contact_person_name','selleremail','mobile',
                                                "sellers.latitude","sellers.longitude", 'street','sellerarea','seller_city','uae_city_emirates.city','emirates',
                                                'uce.city as emirate_name','seller_country')
                                                ->Join('uae_city_emirates','uae_city_emirates.id','sellers.seller_city')
                                                ->join( 'uae_city_emirates as uce', 'uce.id', 'sellers.emirates')
                                                ->first();
        $Order_Details_Arr['shop_address']->latitude    =   floatval ($Order_Details_Arr['shop_address']->latitude);
        $Order_Details_Arr['shop_address']->longitude   =   floatval ($Order_Details_Arr['shop_address']->longitude);
        return $Order_Details_Arr;
    }

    function CustomerOrders($customer_id)
    {
        try
        {
            $customer_orders    =   Order::where('user_id',$customer_id)
                                    // ->where('orders.payment_status','!=','0')
                                    ->latest()
                                    ->get();
            $order_details      =   [];
            if(count($customer_orders) > 0)
            {
                foreach ($customer_orders as $key => $myorder)
                {
                    $order_details[$key]  =   $this-> OrderDetails($myorder->order_id);
                    unset($order_details[$key]['address']);
                }

                $result = ['status'=>true, 'data'=>$order_details];
            }
            else
            {
                throw new Exception("No Orders Placed Yet");
            }
        }
        catch(Exception $e)
        {
            $result     =   ['status'=> false, 'message'=>$e->getMessage()];
        }

        return $result;
    }

    function OrderLists($seller_id, $ordertype, $keyword, $status,$paymentstatus,$paymenttype, $start_dt, $end_dt, $page_limit=25)
    {
        try
        {
            // filtering order id
            $orderlist      =   DB::table('orders')
                                ->select('orders.id','orders.order_id','orders.seller_id','users.name as username','orders.order_status_id',
                                'order_statuses.name as statusname','orders.tax','orders.shipping_charge','orders.payment_status',
                                'orders.processing_fee','orders.transaction_fee','orders.sub_total','orders.commission',
                                'orders.discount','orders.grand_total','orders.payment_type','orders.order_type','orders.created_at')
                                ->leftjoin('users','orders.user_id','=','users.id')
                                ->leftjoin('order_statuses','orders.order_status_id','=','order_statuses.id')
                                // ->where('orders.payment_status','!=','0')
                                ->latest('orders.created_at');
                                // 1- card, 2 -cod


            if($seller_id != null){
                $orderlist->where('orders.seller_id',$seller_id);
            }

            if($ordertype != null){
                $orderlist->where('orders.order_type',$ordertype);
            }

            if($paymentstatus != null){
                $orderlist->where('orders.payment_status',$paymentstatus);
            }

            if($paymenttype != null){
                $orderlist->where('orders.payment_type',$paymenttype);
            }

            if($status != null){
                $orderlist->where('orders.order_status_id',$status);
            }

            if($keyword != null){
                $orderlist->where(function ($q) use ($keyword) {
                        $q->where("orders.order_id","like",'%'.$keyword.'%')
                        ->orWhere("orders.transaction_id","like",'%'.$keyword.'%');
                });
            }

            if($start_dt != null){
                $startDate  = date('Y-m-d H:i:s', strtotime($start_dt));
                if($end_dt == null){
                    throw new Exception('Please Select End Date');
                }
                $endDate    = date('Y-m-d H:i:s', strtotime($end_dt));

                if ($startDate > $endDate){
                    throw new Exception("End date is Greater than Start Date. Please Check");
                }
                $orderlist->where('orders.created_at', '>=', $startDate);
                $orderlist->where('orders.created_at', '<=', $endDate);
            }

            $order_query    =    $orderlist->paginate(25);

            foreach($order_query as $key=>$order){
                // if($order->payment_type=='1' && $order->payment_status=='0'){
                //     unset($order_query[$key]);
                // }
                $order->sellername     = DB::table('sellers')->where('id',$order->seller_id)->first()->seller_full_name_buss;
                $order->status_color   = $this->OrderColorCode($order->order_status_id);
            }
            $result =   ['status'=>true,'orderlist'=>$order_query];
        } catch (\Exception $e) {
            $result =   ['status'=>false,'message'=>$e->getMessage()];
        }
        return $result;
    }

    function DownloadOrderLists($sellerid, $ordertype, $keyword, $status,$paymentstatus, $start_dt, $end_dt){
        try
        {
            DB::statement(DB::raw('set @rownum=0'));
            $orders    =    DB::table('orders')
                            ->select(DB::raw("(@rownum:=@rownum + 1) AS sno"),
                                'orders.order_id','sellers.seller_full_name_buss as shop',
                                'sellers.selleremail','users.name as customername',
                                'users.email as customeremail',
                                DB::raw("IFNULL(tax, '0') as tax"),
                                DB::raw("IFNULL(shipping_charge, '0') as shipping_charge"),
                                DB::raw("IFNULL(processing_fee, '0') as processing_fee"),
                                DB::raw("IFNULL(transaction_fee, '0') as transaction_fee"),
                                DB::raw("IFNULL(orders.commission, 0) as commission"),
                                DB::raw("IFNULL(grand_total, '0') as grand_total"),
                                DB::raw("IFNULL(transaction_id, '-') as transaction_id"),
                                DB::raw("
                                (
                                    CASE
                                        WHEN payment_status='0' THEN 'Pending'
                                        WHEN payment_status='1' THEN 'Paid'
                                        WHEN payment_status='2' THEN 'Refunded'
                                        ELSE '-'
                                    END
                                )   AS payment_status"),
                                DB::raw("
                                (
                                    CASE
                                        WHEN orders.order_type='1' THEN 'Pick Up'
                                        WHEN orders.order_type='2' THEN 'Delivery'
                                        ELSE '-'
                                    END
                                )   AS custordertype"),'order_statuses.name as statusname',
                                'orders.created_at')
                            ->leftJoin('sellers','sellers.id','=','orders.seller_id')
                            ->leftJoin('users','users.id','=','orders.user_id')
                            ->leftjoin('order_statuses','orders.order_status_id','=','order_statuses.id')
                            ->where('orders.payment_status','!=','0')
                            ->latest('orders.created_at');

            if($sellerid != null){
                $orders->where('orders.seller_id',$sellerid);
            }

            if($ordertype != null){
                $orders->where('orders.order_type',$ordertype);
            }

            if($status != null){
                $orders->where('orders.payment_status',$status);
            }

            if($paymentstatus != null){
                $orders->where('orders.payment_status',$paymentstatus);
            }

            if($keyword != null){
                $orders->where(function ($q) use ($keyword) {
                        $q->where("orders.order_id","like",'%'.$keyword.'%')
                        ->orWhere("orders.transaction_id","like",'%'.$keyword.'%');
                });
            }

            if($start_dt != null){
                $startDate  = date('Y-m-d H:i:s', strtotime($start_dt));
                if($end_dt == null){
                    throw new Exception('Please Select End Date');
                }
                $endDate    = date('Y-m-d H:i:s', strtotime($end_dt));

                if ($startDate > $endDate){
                    throw new Exception("End date is Greater than Start Date. Please Check");
                }

                $orders->where('orders.created_at', '>=', $startDate);
                $orders->where('orders.created_at', '<=', $endDate);
            }

            $order_list  = $orders->get();
            $result =   ['status'=>true,'orders'=>$order_list];
        } catch (\Exception $e) {
            $result =   ['status'=>false,'message'=>$e->getMessage()];
        }
        return $result;
    }

    function SellerDownloadOrderLists($sellerid, $ordertype, $keyword, $status,$paymentstatus, $start_dt, $end_dt){
        try
        {
            DB::statement(DB::raw('set @rownum=0'));
            $orders    =    DB::table('orders')
                            ->select(DB::raw("(@rownum:=@rownum + 1) AS sno"),
                                'orders.order_id','users.name as customername',
                                'users.email as customeremail',
                                DB::raw("IFNULL(tax, '0') as tax"),
                                DB::raw("IFNULL(shipping_charge, '0') as shipping_charge"),
                                DB::raw("IFNULL(processing_fee, '0') as processing_fee"),
                                DB::raw("IFNULL(transaction_fee, '0') as transaction_fee"),
                                DB::raw("IFNULL(orders.commission, 0) as commission"),
                                DB::raw("IFNULL(grand_total, '0') as grand_total"),
                                DB::raw("IFNULL(transaction_id, '-') as transaction_id"),
                                DB::raw("
                                (
                                    CASE
                                        WHEN payment_status='0' THEN 'Pending'
                                        WHEN payment_status='1' THEN 'Paid'
                                        WHEN payment_status='2' THEN 'Refunded'
                                        ELSE '-'
                                    END
                                )   AS payment_status"),
                                DB::raw("
                                (
                                    CASE
                                        WHEN orders.order_type='1' THEN 'Pick Up'
                                        WHEN orders.order_type='2' THEN 'Delivery'
                                        ELSE '-'
                                    END
                                )   AS custordertype"),'order_statuses.name as statusname',
                                'orders.created_at')
                            ->leftJoin('sellers','sellers.id','=','orders.seller_id')
                            ->leftJoin('users','users.id','=','orders.user_id')
                            ->leftjoin('order_statuses','orders.order_status_id','=','order_statuses.id')
                            ->where('orders.payment_status','!=','0')
                            ->latest('orders.created_at');

            if($sellerid != null){
                $orders->where('orders.seller_id',$sellerid);
            }

            if($ordertype != null){
                $orders->where('orders.order_type',$ordertype);
            }

            if($status != null){
                $orders->where('orders.payment_status',$status);
            }

            if($paymentstatus != null){
                $orders->where('orders.payment_status',$paymentstatus);
            }

            if($keyword != null){
                $orders->where(function ($q) use ($keyword) {
                        $q->where("orders.order_id","like",'%'.$keyword.'%')
                        ->orWhere("orders.transaction_id","like",'%'.$keyword.'%');
                });
            }

            if($start_dt != null){
                $startDate  = date('Y-m-d H:i:s', strtotime($start_dt));
                if($end_dt == null){
                    throw new Exception('Please Select End Date');
                }
                $endDate    = date('Y-m-d H:i:s', strtotime($end_dt));

                if ($startDate > $endDate){
                    throw new Exception("End date is Greater than Start Date. Please Check");
                }

                $orders->where('orders.created_at', '>=', $startDate);
                $orders->where('orders.created_at', '<=', $endDate);
            }

            $order_list  = $orders->get();
            $result =   ['status'=>true,'orders'=>$order_list];
        } catch (\Exception $e) {
            $result =   ['status'=>false,'message'=>$e->getMessage()];
        }
        return $result;
    }

    function SellerOrderLists($sellerid)
    {
        try
        {
            $order_query    =   DB::table('orders')
                                ->select('orders.id','orders.order_id','orders.seller_id','users.name as username','orders.order_status_id',
                                'order_statuses.name as statusname','orders.tax','orders.shipping_charge','orders.payment_status',
                                'orders.discount','orders.grand_total','orders.payment_type','orders.order_type','orders.created_at')
                                ->join('users','orders.user_id','=','users.id')
                                ->join('order_statuses','orders.order_status_id','=','order_statuses.id')
                                ->latest('orders.created_at')
                                ->where('orders.seller_id',$sellerid)->paginate(25);
            foreach($order_query as $key=>$order)
            {
                $order->sellername = DB::table('sellers')->where('id',$order->seller_id)->first()->seller_full_name_buss;
                $order->status_color   = $this->OrderColorCode($order->order_status_id);
            }
            $result =   ['status'=>true,'orderlist'=>$order_query];
        } catch (\Exception $e) {
            $result =   ['status'=>false,'message'=>$e->getMessage()];
        }
        return $result;
    }

    function TransactionOrderLists($sellerid, $ordertype, $keyword, $status, $start_dt, $end_dt)
    {
        try
        {
            $transactions    =   DB::table('orders')
                                ->select('orders.id','orders.order_id','orders.seller_id',
                                'orders.tax','orders.shipping_charge','orders.payment_status',
                                'orders.processing_fee','orders.transaction_fee','orders.sub_total',
                                'orders.commission', 'orders.discount', 'orders.grand_total', 'orders.transaction_id',
                                'orders.payment_type','orders.order_type','orders.created_at')
                                ->where('payment_status','!=','0')->latest('orders.created_at');
            if($sellerid != null){
                $transactions->where('seller_id',$sellerid);
            }
            if($ordertype != null){
                $transactions->where('order_type',$ordertype);
            }
            if($status != null){
                $transactions->where('payment_status',$status);
            }
            if($keyword != null){
                $transactions->where(function ($q) use ($keyword) {
                        $q->where("order_id","like",'%'.$keyword.'%')
                        ->orWhere("transaction_id","like",'%'.$keyword.'%');
                });
            }

            if($start_dt != null){
                $startDate  = date('Y-m-d H:i:s', strtotime($start_dt));
                if($end_dt == null){
                    throw new Exception('Please Select End Date');
                }
                $endDate    = date('Y-m-d H:i:s', strtotime($end_dt));

                if ($startDate > $endDate){
                    throw new Exception("End date is Greater than Start Date. Please Check");
                }

                $transactions->where('created_at', '>=', $startDate);
                $transactions->where('created_at', '<=', $endDate);
            }
            $transaction_list  = $transactions->paginate(25);

            $result =   ['status'=>true,'transactions'=>$transaction_list];
        } catch (\Exception $e) {
            $result =   ['status'=>false,'message'=>$e->getMessage()];
        }
        return $result;
    }

    function DownloadTransaction($sellerid, $ordertype, $keyword, $status, $start_dt, $end_dt)
    {
        try
        {
            DB::statement(DB::raw('set @rownum=0'));
            $transactions    =   DB::table('orders')
                                ->select(DB::raw("(@rownum:=@rownum + 1) AS sno"),
                                'orders.order_id',
                                DB::raw("IFNULL(tax, '0') as tax"),
                                DB::raw("IFNULL(shipping_charge, '0') as shipping_charge"),
                                DB::raw("IFNULL(processing_fee, '0') as processing_fee"),
                                DB::raw("IFNULL(transaction_fee, '0') as transaction_fee"),
                                DB::raw("IFNULL(orders.commission, 0) as commission"),
                                DB::raw("IFNULL(grand_total, '0') as grand_total"),
                                DB::raw("IFNULL(transaction_id, '-') as transaction_id"),
                                DB::raw("
                                (
                                    CASE
                                        WHEN payment_status='1' THEN 'Paid'
                                        WHEN payment_status='2' THEN 'Refunded'
                                        ELSE '-'
                                    END
                                )   AS payment_status"),
                               'orders.created_at')
                                ->where('orders.payment_status','!=','0')
                                ->latest('orders.created_at');

            if($sellerid != null){
                $transactions->where('orders.seller_id',$sellerid);
            }

            if($ordertype != null){
                $transactions->where('order_type',$ordertype);
            }

            if($status != null){
                $transactions->where('orders.payment_status',$status);
            }

            if($keyword != null){
                $transactions->where(function ($q) use ($keyword) {
                        $q->where("orders.order_id","like",'%'.$keyword.'%')
                        ->orWhere("orders.transaction_id","like",'%'.$keyword.'%');
                });
            }

            if($start_dt != null){
                $startDate  = date('Y-m-d H:i:s', strtotime($start_dt));
                if($end_dt == null){
                    throw new Exception('Please Select End Date');
                }
                $endDate    = date('Y-m-d H:i:s', strtotime($end_dt));

                if ($startDate > $endDate){
                    throw new Exception("End date is Greater than Start Date. Please Check");
                }

                $transactions->where('orders.created_at', '>=', $startDate);
                $transactions->where('orders.created_at', '<=', $endDate);
            }

            $transaction_list  = $transactions->get();

            $result =   ['status'=>true,'transactions'=>$transaction_list];
        } catch (\Exception $e) {
            $result =   ['status'=>false,'message'=>$e->getMessage()];
        }
        return $result;
    }

    function SellerOrders($sellerid)
    {
        $seller_orders  =   DB::table('order_vendors')
                            ->select('order_vendors.*', 'products.name as productname','order_statuses.name as statusname')
                            ->join('products','order_vendors.productid','=','products.id')
                            ->join('order_statuses','order_statuses.id','=','order_vendors.orderstatus')
                            ->where('order_vendors.sellerid',$sellerid)
                            ->paginate(25);
        return $seller_orders;
    }

    public function GetOrderStatus()
    {
        try {
            $order_type     = request()->order_type;
            $curr_status    = request()->curr_status;
            if($curr_status == 2){
                $OrderStatus    =   DB::table('order_statuses')
                                    ->where('show_n_list','0')
                                    ->where('active_status','1')
                                    ->get();
            } else {
                $OrderStatus    =   DB::table('order_statuses')
                                    ->where('show_n_list','1')
                                    ->where('active_status','1')
                                    ->whereIn('order_type',['0',$order_type])
                                    ->get();
            }
            // print("<pre>");
            // print_r($OrderStatus);die;
            $result =   ['status'=>true, 'data'=>$OrderStatus];
        } catch (\Exception $e) {
            $result =   ['status'=>false, 'message'=>$e->getMessage()];
        }
        return response()->json($result);
    }

    public function UpdateOrderStatus()
    {
        try {
            $input  =   request()->all();
            if($input['curr_status_id'] == 1){
                $order_status = $input['verifyorder_status'];
            }
            else{
                $order_status = $input['order_status'];
            }

            if($order_status > 10){
                throw new Exception("Invalid Order Status");
            }
            $UpdateOrderVendors =   DB::table('order_vendors')
                                    ->where('orderid',$input['curr_order_id'])
                                    ->update([
                                        'orderstatus'=>$order_status,
                                        'updated_at'=>  date('Y-m-d H:i:s')
                                    ]);
            if(!$UpdateOrderVendors){
                throw new Exception("Something Went Wrong in Updating");
            }

            $update_orders =    [
                                    'order_status_id'=>$order_status,
                                    'updated_at'=>  date('Y-m-d H:i:s')
                                ];
            if($order_status == 10){
                $update_orders['payment_status']  =   '2';
            }
            $UpdateOrderStatus =   DB::table('orders')
                                    ->where('order_id',$input['curr_order_id'])
                                    ->update($update_orders);

            if(!$UpdateOrderStatus){
                throw new Exception("Something Went Wrong in Updating");
            }
            if($input['remarks'] != null){
                $remarks    =   $input['remarks'];
            } else {
                $remarks    =   '-';
            }
            $status_track_data  =   [
                'sub_order_id'  => $input['curr_order_id'],
                'order_status'  => $order_status,
                'remarks'       => $remarks,
                'created_at'    => date('Y-m-d H:i:s')
            ];


            OrderStatusTrack::insert($status_track_data);
            $order_det = $this->OrderDetails($input['curr_order_id']);
            $this->sendOrderMail($order_det);
            $result = ['status'=>true, 'Message'=>"Status Updated Successfully"];
        } catch (\Exception $e) {
            $result = ['status'=>false, 'message'=>$e->getMessage()];
        }

        return response()->json($result);
    }

    public function GetOrderTrack()
    {
        try {
            $get_track_details      =   OrderStatusTrack::join('order_statuses','order_status_tracks.order_status','=','order_statuses.id')
                                        ->where('sub_order_id',request()->sub_order_id)
                                        ->select('order_status_tracks.*','order_statuses.name as status_name')->get();
            foreach($get_track_details as $track){
                $track->orderdate  =   Carbon::parse($track->created_at)->format('D, M d, Y h:i A');
            }
            $result = ['status'=>true, 'data'=>$get_track_details];
            if(!$get_track_details){
                throw new Exception("Something Went Wrong in Updating");
            }
        } catch (\Exception $e) {
            $result = ['status'=>false, 'message'=>$e->getMessage()];
        }

        // dd($result);
        return response()->json($result);
    }

    public function UpdatePaymentStatus()
    {
        try
        {
            $order_id               =   request()->order_id;

            $update_data            =   [
                'payment_status'    =>  '1',
                'updated_at'        =>  date('Y-m-d H:i:s')
            ];
            $update = Order::where('order_id',$order_id)->Update($update_data);
            if(!$update){
                throw new Exception('Something Went Wrong. Try again later');
            }
            $result =   ['status'=>true,'message'=>'Payment Recieved Successfully'];
        }
        catch (\Exception $e)
        {
            $result     = ['status'=>false,'message'=> $e->getMessage()];
        }

        if($result['status'] == true) {
            return redirect()->back()->with('success',$result['message']);
        }else{
            return redirect()->back()->with('error',$result['message']);
        }
    }

    public function OrderInvoice($orderid){
        try{
            $CheckOrderId   = Order::find($orderid);
            if ($CheckOrderId) {
                $order_details = $this->OrderDetails($CheckOrderId->order_id);

                $result = ['status'=>true,'data'=> $order_details  , "Message"=>"Order Details"];
            }
            else{
                throw new Exception("Please Check the Order Id");
            }
        }
        catch (\Exception $e)
        {
            $result     = ['status'=>false,'message'=> $e->getMessage()];
        }

        if($result['status'] == true) {
            $order_det      = $result['data'];
            return view('dashboard.commonly_used.order_invoice',compact('order_det'));
        }else{
            redirect()->back()->with('error',$result['message']);
        }
    }

    public function sendOrderMail($order_det){
        $order_type = '';
        if($order_det['order']->order_type == '1'){
            $order_type =   'Pickup';
        } else {
            $order_type =   'Deliver';
        }

        $trackmsg   =   OrderStatusTrack::where('sub_order_id',$order_det['order']->order_id)
                        ->where('order_status',$order_det['order']->order_status_id)->first()->remarks;

        if($order_type == '1'){
            $address    =   ['shopname'=>$order_det['shop_address']->shopname,'area'=>$order_det['shop_address']->sellerarea,'city'=>$order_det['shop_address']->city,'mobile'=>$order_det['shop_address']->mobile];
        } else {
            $address    =   $order_det['address']->address;
        }
        $status_id  =   $order_det['order']->order_status_id;
        $order_id   =   $order_det['order']->order_id;
        $subject    =   '';
        if($status_id == 1){
            $subject    =   "Order Placed #".$order_id."";
        } else if($status_id ==  2){
            $subject    =   "Order Declined #".$order_id."";
        } else if($status_id ==  3){
            $subject    =   "Order Accepted #".$order_id."";
        }else if($status_id ==  5){
            $subject    =   "Order Ready #".$order_id."";
        }else if($status_id ==  6){
            $subject    =   "Order Ready #".$order_id."";
        }else if($status_id ==  7){
            $subject    =   "Order Delivered #".$order_id."";
        }else if($status_id ==  8){
            $subject    =   "Order Completed #".$order_id."";
        }else if($status_id ==  9){
            $subject    =   "Order Refund Completed #".$order_id."";
        }
        $data = [
            'admin_mail'=>DB::table('admins')->where('is_super','1')->first()->email,
            'from'=>env('MAIL_FROM_ADDRESS'),
            'subject'=>$subject,
            'user_name'=>$order_det['user_det']->name,
            'user_email'=>$order_det['user_det']->email,
            'seller_name'=> $order_det['shop_address']->shopname,
            'seller_email'=> $order_det['shop_address']->selleremail,
            'order_id' =>$order_id,
            'myorderid'=>$order_det['myorderid'],
            'status_id'=>$status_id,
            'status_name'=> strtoupper($order_det['order']->statusname),
            'track_msg'=>$trackmsg,
            'grand_total'=>$order_det['order']->grand_total,
            'order_type'=>$order_type,
            'address'=>$address
        ];

        Mail::send('emails.order_status_mail_customer', ["data"=>$data], function($message) use($data) {
            $message->to($data['user_email']);
            $message->from(env('MAIL_FROM_ADDRESS'), env('APP_NAME'));
            $message->subject($data['subject']);
        });

        if($status_id == 1){
            Mail::send('emails.order_status_mail_seller_admin', ["data"=>$data],
            function($message) use($data) {
                $message->to($data['seller_email']);
                $message->cc($data['admin_mail']);
                $message->from(env('MAIL_FROM_ADDRESS'), env('APP_NAME'));
                $message->subject($data['subject']);
            });
        }

    }
}
