<?php

namespace App\Http\Controllers;

use App\Traits\OrderTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{
    use OrderTrait;
    //
    public function OrderList(Request $request)
    {
        $get_orders = $this->OrderLists();
        if($get_orders['status'] == true){
            return view('dashboard.admin.orders.index')->with([
                    'GetOrderDetails'=>$get_orders['orderlist'],
                    'order_status_list'=>$get_orders['orderstatus']
                ]);
        }else{
            return redirect()->back()->with('error',$get_orders['message']);
        }
    }

}
