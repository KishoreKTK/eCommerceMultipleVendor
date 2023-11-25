<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductStocks;
use App\Models\Seller;
use Exception;
// use Validator;
// use Dotenv\Validator;
use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class CartAPIController extends Controller
{
    public function CartList(){
        try
        {
            $user_id        =   auth()->guard('api')->user()->id;
            $cartlist       =   Cart::where('user_id',$user_id)
                                ->leftjoin('products','products.id','carts.product_id')
                                ->leftjoin('product_stocks',
                                    'product_stocks.product_entry','carts.product_entry_id')
                                ->select(
                                    'products.id','products.image','products.name',
                                    'products.seller_id', 'product_stocks.quantities as avaialble_qty',
                                    'products.description', 'product_stocks.product_price',
                                    'product_stocks.product_entry','carts.options',
                                    'product_stocks.min_order_qty','carts.product_qty as selected_qty'
                                )
                                ->get();

            if(count($cartlist) > 0)
            {

                $total_price_amount     =   0;
                $seller_ids             =   [];

                foreach($cartlist as $cart){
                    array_push($seller_ids,$cart->seller_id);
                    $options =    json_decode($cart->options);
                    foreach($options as $op){
                        // $op->attr_id    =   intval($op->attr_id);
                        $op->option_id  =   intval($op->option_id);
                    }

                    $cart->options       =  $options;
                    $cart->seller_name   =   Seller::where('id',$cart->seller_id)->first()->seller_full_name_buss;

                    $cart->total_price   =   $cart->selected_qty * $cart->product_price;
                    $total_price_amount  =   $total_price_amount + $cart->total_price;
                }

                $result = ['status'=>true,'count'=>count($cartlist),'data'=> $cartlist,
                            'total_amount'=>$total_price_amount,
                            'message'=>'Cart Products Listed successfully'];
            }
            else
            {
                $result = ['status'=>false, 'message'=>'No Products in Cart'];
            }
        }
        catch (\Exception $e)
        {
            $result = ['status'=>false,'message'=> $e->getMessage()];
        }
        return response()->json($result);
    }

    public function AddToCart(Request $request)
    {
        try
        {
            $validator = FacadesValidator::make($request->all(),[
                'product_id' => 'required|exists:products,id',
                'product_qty' => 'required',
                "product_entry_id"=>"required",
                "product_options"=>"required"
            ]);

            if($validator->fails()) {
                $result = ['status'=>false,'message'=>  implode( ", ",$validator->errors()->all())];
            } else {
                $dt 			    =   new \DateTime();
                $datetime		    =   $dt->format('Y-m-d H:i:s');
                $product_id         =   $request->product_id;
                $user_id            =   auth()->guard('api')->user()->id;
                $product_qty        =   $request->product_qty;
                $product_entry_id   =   $request->product_entry_id;

                $check_product_seller       =   DB::table('products')
                                                ->where('id',$request->product_id)
                                                ->first()->seller_id;
                $existing_product_n_cart    =   Cart::where('user_id',$user_id)->get();

                foreach($existing_product_n_cart as $exisitng_prdt){
                    $existing_product_id    =   DB::table('products')
                                                ->where('id',$exisitng_prdt->product_id)
                                                ->pluck('seller_id')->toArray();
                    $unique_selller_id      =   array_unique($existing_product_id);
                    if(count($unique_selller_id)>1){
                        throw new Exception("Please Select Products from Same Seller");
                    }
                    // Check Current Product Seller with Existing Cart Products Seller
                    if($check_product_seller !=  $unique_selller_id[0]){
                        throw new Exception("Please Select Products from Same Seller");
                    }
                }

                $check_available_product    =   Cart::where('product_id',$product_id)
                                                ->where('user_id',$user_id)
                                                ->where('product_entry_id',$product_entry_id)
                                                ->count();

                if($check_available_product > 0) {
                    $result = ['status'=>false,'message'=> "Product already Available in Cart"];
                } else {
                    $check_Quantity =   ProductStocks::where('product_id','=',$product_id)
                                        ->where('quantities','>', $product_qty)
                                        ->where('product_entry',$product_entry_id)
                                        ->first();

                    if($check_Quantity) {
                        $input  = [
                                    'product_id'=>$product_id ,'user_id'=>$user_id , 'product_qty'=>$product_qty,
                                    'options'=>json_encode(request()->product_options),'product_entry_id'=>$product_entry_id, "created_at"=>$datetime,"updated_at"=>$datetime];
                        Cart::create($input);
                        $result = ['status'=>true,'message'=> "Product Added to Cart"];
                    } else {
                        $result = ['status'=>false,'message'=> "Out of Stock / Please Reduce Quantity"];
                    }
                }
            }
        }
        catch (\Exception $e)
        {
            $result = ['status'=>false,'message'=> $e->getMessage()];
        }
        return response()->json($result);
    }

    public function QuantityChanges(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(),[
                'product_id' => 'required|exists:products,id',
                'product_qty' => 'required',
                "product_entry_id"=>'required'
            ]);
            if($validator->fails())
            {
                $result = ['status'=>false,'message'=> implode( ", ",$validator->errors()->all())];
            }
            else
            {
                $dt 			=   new \DateTime();
                $datetime		=   $dt->format('Y-m-d H:i:s');
                $product_id     =   $request->product_id;
                $user_id        =   auth()->guard('api')->user()->id;
                $product_qty    =   $request->product_qty;
                $check_Quantity =   Product::where('products.id','=',$product_id)
                                    ->join('product_stocks','product_stocks.product_id','=','products.id')
                                    ->where('product_stocks.quantities','>', $product_qty)
                                    ->where('product_stocks.product_entry',request()->product_entry_id)
                                    ->first();
                if($check_Quantity)
                {
                    $check_product_n_cart = Cart::where('product_id',$product_id)->where('user_id',$user_id)
                                            ->where('product_entry_id',request()->product_entry_id)->first();
                    if(!$check_product_n_cart){
                        throw new Exception("Please Add the Produt to Cart to Update Qunantity");
                    }
                    else
                    {
                        $input  = ['product_qty'=>$product_qty ,"updated_at"=>$datetime];
                        Cart::where('product_id',$product_id)->where('user_id',$user_id)
                        ->where('product_entry_id',request()->product_entry_id)->Update($input);
                        $result = ['status'=>true,'message'=> "Quantity Updated to Cart"];
                    }
                }
                else
                {
                    $result = ['status'=>false,'message'=> "Out of Stock / Please Reduce Quantity / Product  Not Available In Cart"];
                }
            }
        }
        catch (\Exception $e)
        {
            $result = ['status'=>false,'message'=> $e->getMessage()];
        }
        return response()->json($result);
    }


    public function RemoveFromCart(Request $request){
        try
        {
            $validator = Validator::make($request->all(),[
                'product_id' => 'required|exists:products,id',
                'product_entry_id'=>'required'
            ]);
            if($validator->fails())
            {
                $result = ['status'=>false,'message'=> implode( ", ",$validator->errors()->all())];
            }
            else
            {
                $product_id     =   $request->product_id;
                $user_id        =   auth()->guard('api')->user()->id;
                $cart           =   Cart::where('product_id',$product_id)->where('user_id',$user_id)
                                    ->where('product_entry_id',request()->product_entry_id);
                if($cart->exists())
                {
                    $cart->delete();
                    $result = ['status'=>true,'message'=> "Procuct Removed from Cart"];
                }
                else
                {
                    $result = ['status'=>false,'message'=> "Procuct Not Found in Cart"];
                }
            }
        }
        catch (\Exception $e)
        {
            $result = ['status'=>false,'message'=> $e->getMessage()];
        }
        return response()->json($result);
    }


    public function ClearCart(){
        try
        {
            $user_id        =   auth()->guard('api')->user()->id;
            $cart = Cart::where('user_id',$user_id);
            if($cart->exists())
            {
                $cart->delete();
                $result = ['status'=>true,'message'=> "Cart Items Cleared"];
            }
            else
            {
                $result = ['status'=>false,'message'=> "No Products in Cart"];
            }
        }
        catch (\Exception $e)
        {
            $result = ['status'=>false,'message'=> $e->getMessage()];
        }
        return response()->json($result);
    }

}
