<?php

namespace App\Http\Controllers\Seller;
use Session;
use App\Exports\OrdersExport;
use App\Exports\SellerOrdersExport;
use App\Exports\TransactionExport;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\OrderStatusTrack;
use App\Models\OrderVendor;
use App\Traits\OrderTrait;
use App\Traits\ProductTrait;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class SellerOrderController extends Controller
{
    use OrderTrait, ProductTrait;

    public function OrderList(Request $request)
    {
        $seller_id       =   session()->get('seller_id');
        foreach (Order::where('read', '=',0)->where('seller_id',$seller_id)->get() as $data)
        {
            $read=Order::where("id",$data->id)->update(["read"=>1]);
        }
        Session::put('unread_orders', 0);

        if(request()->has('status')){
            $status     =   request()->status;
        } else{
            $status     =   null;
        }
        if(request()->has('keyword')){
            $keyword    =   request()->keyword;
        } else{
            $keyword    =   null;
        }

        if(request()->has('ordertype')){
            $ordertype     =   request()->ordertype;
        } else{
            $ordertype     =   null;
        }

        if(request()->has('paymentstatus')){
            $paymentstatus     =   request()->paymentstatus;
        } else{
            $paymentstatus     =   null;
        }

        if(request()->has('paymenttype')){
            $paymenttype     =   request()->paymenttype;
        } else{
            $paymenttype     =   null;
        }


        if(request()->has('startdate')) {
            $start_dt   =   request()->startdate;
        } else{
            $start_dt   =   null;
        }
        if(request()->has('enddate')) {
            $end_dt   =   request()->enddate;
        } else{
            $end_dt   =   null;
        }
        $dta = ['seller_id'=>$seller_id,'ordertype'=>$ordertype,'keyword'=>$keyword,'status'=>$status,'payment_status'=>$paymentstatus,'start_dt'=>$start_dt,'end_dt'=>$end_dt];
        // dd($dta);
        $get_orders     =   $this->OrderLists($seller_id, $ordertype, $keyword, $status,$paymentstatus, $paymenttype, $start_dt, $end_dt);
        dd($get_orders);
        $seller_list    =   $this->SelectSellerList();
        $order_status   =   $this->Orderstuatuslist();
        $GetOrderDetails=   $get_orders['orderlist'];

        if($get_orders['status'] == true){
            return view('dashboard.commonly_used.orderlist',compact('GetOrderDetails','order_status','seller_list', 'paymentstatus',
            'seller_id','status','keyword','ordertype','paymenttype','start_dt','end_dt'));
        }else{
            redirect()->back()->with('error',$get_orders['message']);
        }
    }

    public function ExportOrders()
    {
        $seller_id       =   session()->get('seller_id');

        if(request()->has('status')){
            $status     =   request()->status;
        } else{
            $status     =   null;
        }

        if(request()->has('keyword')){
            $keyword    =   request()->keyword;
        } else{
            $keyword    =   null;
        }

        if(request()->has('ordertype')){
            $ordertype     =   request()->ordertype;
        } else{
            $ordertype     =   null;
        }

        if(request()->has('paymentstatus')){
            $paymentstatus     =   request()->paymentstatus;
        } else{
            $paymentstatus     =   null;
        }

        if(request()->has('paymenttype')){
            $paymenttype     =   request()->paymenttype;
        } else{
            $paymenttype     =   null;
        }

        if(request()->has('startdate')) {
            $start_dt   =   request()->startdate;
        } else{
            $start_dt   =   null;
        }

        if(request()->has('enddate')) {
            $end_dt   =   request()->enddate;
        } else{
            $end_dt   =   null;
        }

        $get_orders     =   $this->SellerDownloadOrderLists($seller_id, $ordertype, $keyword, $status,$paymentstatus,$paymenttype, $start_dt, $end_dt);

        if($get_orders['status'] == true){
            return Excel::download(new SellerOrdersExport($get_orders['orders']), 'OrderReport.xlsx');
        }else{
            redirect()->back()->with('error',$get_orders['message']);
        }
    }

    public function OrderDetail($orderid){
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

            return view('dashboard.commonly_used.order_det',compact('order_det'));
        }else{
            redirect()->back()->with('error',$result['message']);
        }
    }

    public function TransactionList(){
        $sellerid           =   session()->get('seller_id');
        if(request()->has('status')){
            $status     =   request()->status;
        } else{
            $status     =   null;
        }

        if(request()->has('ordertype')){
            $ordertype     =   request()->ordertype;
        } else{
            $ordertype     =   null;
        }

        if(request()->has('keyword')){
            $keyword    =   request()->keyword;
        } else{
            $keyword    =   null;
        }
        if(request()->has('startdate')) {
            $start_dt   =   request()->startdate;
        } else{
            $start_dt   =   null;
        }
        if(request()->has('enddate')) {
            $end_dt   =   request()->enddate;
        } else{
            $end_dt   =   null;
        }

        $get_orders         =   $this->TransactionOrderLists($sellerid, $ordertype, $keyword, $status, $start_dt, $end_dt);
        $GetOrderDetails    =   $get_orders['transactions'];
        if($get_orders['status'] == true){
            return view('dashboard.commonly_used.Transaction',compact('GetOrderDetails','ordertype','status','keyword','start_dt','end_dt'));
        }else{
            redirect()->back()->with('error',$get_orders['message']);
        }
    }

    public function  ExportTransaction(){
        $sellerid       =   session()->get('seller_id');
        if(request()->has('status')){
            $status     =   request()->status;
        } else{
            $status     =   null;
        }

        if(request()->has('ordertype')){
            $ordertype     =   request()->ordertype;
        } else{
            $ordertype     =   null;
        }

        if(request()->has('keyword')){
            $keyword    =   request()->keyword;
        } else{
            $keyword    =   null;
        }

        if(request()->has('startdate')) {
            $start_dt   =   request()->startdate;
        } else{
            $start_dt   =   null;
        }

        if(request()->has('enddate')) {
            $end_dt   =   request()->enddate;
        } else{
            $end_dt   =   null;
        }

        $get_transaction    =   $this->DownloadTransaction($sellerid, $ordertype, $keyword, $status, $start_dt, $end_dt);

        if($get_transaction['status'] == true){
            return Excel::download(new TransactionExport($get_transaction['transactions']), 'TransactionReport.xlsx');
        }else{
            redirect()->back()->with('error',$get_transaction['message']);
        }
    }

}
