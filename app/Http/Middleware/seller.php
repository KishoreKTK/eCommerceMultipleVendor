<?php

namespace App\Http\Middleware;
use Auth;
use Closure;
use Session;
use App\Models\Order;
use Illuminate\Http\Request;

class seller
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $seller=Auth::guard('seller')->user();
        if(isset($seller)&& !empty($seller))
        {
            $seller_id       =   session()->get('seller_id');
            $booking=Order::where("read",0)->where('seller_id',$seller_id)->count();

            Session::put('unread_orders', $booking);
            return $next($request);

        }
        else
        {
            return redirect()->route('seller.login');


        }

    }
}
