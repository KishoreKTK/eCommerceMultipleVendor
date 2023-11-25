<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Seller;
use App\Models\SellerCategories;
use App\Models\UserFavShop;
use Exception;
use Illuminate\Support\Facades\Validator;

class SellerAPIController extends Controller
{

    //
    public function sellers()
    {
        try
        {
            $user        =  auth()->guard('api')->user();
            if($user)
            {
                $user_id    =   $user->id;
            } else {
                $user_id    =   0;
            }
            $sellers        =   DB::table('sellers')
                                ->select('sellers.id as shop_id','sellers.seller_full_name_buss as shopname',
                                'sellers.sellerarea','sellers.sellerprofile as profile',
                                'sellers.pickup','sellers.delivery','sellers.latitude','sellers.longitude',
                                'sellers.seller_city as seller_city_id',
                                'sellers.emirates as emirate_id','sellers.seller_trade_exp_dt')
                                ->where('sellers.approval','=','1')
                                ->whereNull('sellers.deleted_at')
                                ->where('sellers.is_active','=','1')->get();

            foreach($sellers as $key=>$data)
            {
                // if Trade License Expired Not Shown
                if(strtotime($data->seller_trade_exp_dt) < strtotime('now')  && $data->seller_trade_exp_dt!=NULL){
                    unset($sellers[$key]);
                }

                // If no Latitude and Longitude Available
                if($data->latitude == null && $data->longitude == null){
                    unset($sellers[$key]);
                }

                // If no Delivery and Pickup Available Not Shown
                if($data->pickup == 0 && $data->delivery == 0){
                    unset($sellers[$key]);
                }

                // if Delivery Yes and No Delivery Area Updated Not Shown
                if($data->delivery == 1){
                    $check_shipping_locations = DB::table('seller_shipping_details')->where('seller_id',$data->shop_id)->count();
                    if($check_shipping_locations == 0){
                        unset($sellers[$key]);
                    }
                }

                // if No Products Available don't Show
                $check_avail_products = DB::table('products')->where('seller_id',$data->shop_id)
                                        ->where('status','1')->count();
                if($check_avail_products == 0){
                    unset($sellers[$key]);
                }

                // // if No Banners Available don't Show
                // $check_avail_banners  = DB::table('shop_images')->where('shop_id',$data->shop_id)->count();
                // if($check_avail_banners == 0){
                //     unset($sellers[$key]);
                // }

                // Check if No Category Available Not to Show
                $data->seller_categories    =   SellerCategories::
                                                leftjoin('categories','categories.id',
                                                'seller_categories.category_id')
                                                ->where('seller_id',$data->shop_id)
                                                ->whereNull('categories.deleted_at')
                                                ->where('categories.is_active','1')
                                                ->groupBy('seller_categories.category_id')
                                                ->pluck('category_id')->toArray();

                if(count($data->seller_categories) == 0){
                    unset($sellers[$key]);
                }

            }

            $filtered_seller_ids = [];
            foreach($sellers as $shop){
                array_push($filtered_seller_ids, $shop->shop_id);
            }

            if(request()->has('is_favorite') && request()->is_favorite == 1){
                $sellers    =   DB::table('user_fav_shops')
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
                                ->where('user_id',$user_id)
                                ->whereIn('shop_id',$filtered_seller_ids)
                                ->groupBy('shop_id');
            } else {
                $sellers    =   DB::table('sellers')
                                ->select('sellers.id as shop_id','sellers.sellername as shopname',
                                    'sellers.seller_full_name_buss as shopname',
                                    'sellers.sellerprofile as profile','sellers.sellerarea',
                                    'sellers.seller_city as seller_city_id','sellers.pickup','sellers.delivery',
                                    'uae_city_emirates.city as seller_city_name',
                                    'sellers.emirates as emirate_id', 'sellers.seller_trade_exp_dt',
                                    'uce.city as emirates')
                                ->Join('uae_city_emirates','uae_city_emirates.id','sellers.seller_city')
                                ->join( 'uae_city_emirates as uce', 'uce.id', 'sellers.emirates')
                                ->where('sellers.approval','=','1')
                                ->whereNull('sellers.deleted_at')
                                ->where('sellers.is_active','=','1')
                                ->whereIn('sellers.id',$filtered_seller_ids);
            }

            if(request()->has('keyword') && request()->keyword != ''){
                $sellers->Where("sellers.seller_full_name_buss","like",'%'.request()->keyword.'%');
            }
            if(request()->has('category_id') && request()->category_id != ''){
                $category_id    =   request()->category_id;
                $Check_Cat_id   =   Categories::where('is_active','1')->find($category_id);
                if(!$Check_Cat_id){
                    throw new Exception("Please Check Category Id");
                }
                $seller_ids     =   SellerCategories::whereIn('category_id',$category_id)->pluck('seller_id')->toArray();
                if(count($seller_ids) == 0) {
                    throw new Exception("No Shops Available in this Category");
                }
                $sellers->whereIn('sellers.id',$seller_ids);
            }

            $sellerlist =   $sellers->orderBy('sellers.id','asc')->get();

            if(count($sellerlist) > 0)
            {
                foreach($sellerlist as $key=>$data)
                {
                    $check_avail_products       =   DB::table('products')->where('seller_id',$data->shop_id)
                                                    ->where('status','1')->count();

                    $data->avil_prod_count      =   $check_avail_products;

                    $check_fav_shop             =   UserFavShop::where('user_id',$user_id)
                                                    ->where('shop_id',$data->shop_id)->exists();

                    if($check_fav_shop)
                    {
                        $data->fav_shop  = true;
                    }else{
                        $data->fav_shop  = false;
                    }
                    $data->profile              =   asset($data->profile);

                }

                if(count($sellerlist) == 0) {
                    throw new Exception("No Shops Available");
                }
                $result         = ['status'=>true,'count'=>count($sellerlist),'data'=>  $sellerlist, 'message'=>'Shops testing Listed Successfully'];
            }
            else
            {
                $result         = ['status'=>false, 'message'=>'No Shops found'];
            }
        }
        catch (\Exception $e)
        {
            $result = ['status'=>false,'message'=> $e->getMessage()];
        }
        return response()->json($result);
    }

    public function sellerdetail($id)
    {
        try
        {
            $seller     =   Seller::join('uae_city_emirates','uae_city_emirates.id','sellers.seller_city')
                            ->join( 'uae_city_emirates as uce', 'uce.id', 'sellers.emirates')
                            ->select('sellers.*','uae_city_emirates.city as city_name','uce.city as emirate_name')
                            ->where('sellers.approval','=','1')
                            ->where('sellers.is_active','=','1')
                            ->whereNull('sellers.deleted_at')
                            ->find($id);
            if(!$seller) {
                throw new Exception("Please Check Seller Id");
            }

            $seller->sellerprofile      =   asset($seller->sellerprofile);
            $products                   =   DB::table('products')
                                            ->leftjoin('product_stocks','products.id','=','product_stocks.product_id')
                                            ->leftJoin('categories','products.category_id','=','categories.id')
                                            ->where('products.status','1')
                                            ->where('product_stocks.price_type','1')
                                            ->select('products.id','products.image','products.name','products.category_id',
                                            'categories.name as category_name','products.seller_id',
                                            'product_stocks.product_price','products.is_featured')
                                            ->where('products.seller_id','=',$seller->id)
                                            ->groupBy('products.id')->get();

            $seller->products           =   $products;
            foreach( $seller->products as $prd){
                $prd->image             =   asset($prd->image);
            }
            $seller_categories          =   DB::table('seller_categories')
                                            ->leftJoin('categories','categories.id','=','seller_categories.category_id')
                                            ->where('seller_id',$seller->id)
                                            ->leftjoin(DB::raw('(SELECT
                                                    p.category_id,
                                                    COUNT(p.id) AS productcount
                                                FROM
                                                products AS p
                                                where p.status = "1"
                                                GROUP BY p.category_id
                                            ) AS producttbl'),
                                            function($join)
                                            {
                                                $join->on('seller_categories.category_id', '=', 'producttbl.category_id');
                                            })
                                            ->select('seller_categories.category_id',
                                            'categories.name','categories.image_url as image',
                                            DB::raw("IFNULL(producttbl.productcount, '0') as ProductCount"))
                                            ->where('categories.is_active','1')
                                            ->where('ProductCount','!=',0)
                                            ->whereNull('categories.deleted_at')
                                            ->groupBy('seller_categories.category_id')
                                            ->get();

            foreach( $seller_categories as $cat){
                $cat->image  =   asset($cat->image);
            }

            $seller->categories     =   $seller_categories;

            $shop_banner_images  =   [];
            $seller_banners         =   DB::table('shop_images')->select('image_urls as image')
                                        ->where('shop_id',$seller->id)->get();
            if(count($seller_banners)> 0){
                foreach($seller_banners as $banners){
                    $shop_banner_images[]  =    asset($banners->image);
                 }
            } else {
                $shop_banner_images[]  =   asset($seller->sellerprofile);
            }

            $seller->banners        =   $shop_banner_images;

            $seller->seller_shipping_city       =   DB::table('seller_shipping_details')->whereNull('deleted_at')
                                                    ->leftJoin('uae_city_emirates','uae_city_emirates.id',
                                                                'seller_shipping_details.to_city')
                                                    ->select('seller_shipping_details.*','uae_city_emirates.city as to_city_name')
                                                    ->where('seller_id',$id)->get();

            $user        =  auth()->guard('api')->user();
            if($user)
            {
                $user_id    =   $user->id;
            } else {
                $user_id    =   0;
            }
            // $user_id                =   auth()->guard('api')->user()->id;
            $check_fav_shop         =   UserFavShop::where('user_id',$user_id)
                                        ->where('shop_id',$seller->id)->exists();

            if($check_fav_shop){
                $seller->fav_shop   = true;
            }else{
                $seller->fav_shop   = false;
            }

            $bannerdata         =   [];

            $featured_products  =   [];
            $featured_products['bannerid']      =   '1';
            $featured_products['bannertype']    =   'products';
            $featured_products['title']         =   "Recent Featured Products";
            $featured_products_data             =    DB::table('products')
                                                    ->leftjoin('sellers','products.seller_id','=','sellers.id')
                                                    ->leftjoin('product_stocks','products.id','=','product_stocks.product_id')
                                                    ->leftJoin('categories','products.category_id','=','categories.id')
                                                    ->where('products.status','1')
                                                    ->where('sellers.is_active','1')
                                                    ->where('categories.is_active','1')
                                                    ->where('products.seller_id',$seller->id)
                                                    ->select('products.id as product_id','products.image','products.name','products.category_id',
                                                    'categories.name as category_name','products.seller_id as shop_id','sellers.sellername as shopname',
                                                    'product_stocks.product_price','products.is_featured')->where('products.is_featured','=','1')
                                                    ->orderby('products.created_at','desc')->take(5)->get();
            foreach($featured_products_data as $featured_prd){
                $featured_prd->image = asset($featured_prd->image);
            }
            $featured_products['bannerdata']    =   $featured_products_data;
            array_push($bannerdata, $featured_products);


            $top_sold_products  =   [];
            $top_sold_products['bannerid']      =   '2';
            $top_sold_products['bannertype']    =   'products';
            $top_sold_products['title']         =   "Top Sold Products";
            $tolp_sold_product_data             =    DB::table('products')
                                                    ->leftjoin('sellers','products.seller_id','=','sellers.id')
                                                    ->leftjoin('product_stocks','products.id','=','product_stocks.product_id')
                                                    ->leftJoin('categories','products.category_id','=','categories.id')
                                                    ->where('products.status','1')
                                                    ->where('sellers.is_active','1')
                                                    ->where('categories.is_active','1')
                                                    ->where('products.seller_id',$seller->id)
                                                    ->select('products.id as product_id','products.image','products.name','products.category_id',
                                                    'categories.name as category_name','products.seller_id','sellers.sellername',
                                                    'product_stocks.product_price','products.is_featured','salecount as ordercount')
                                                    ->join(DB::raw('(SELECT
                                                        productid,
                                                        SUM(prod_qty) AS salecount
                                                    FROM
                                                        order_vendors AS OV
                                                    GROUP BY productid
                                                ) AS product_sold_tbl'),
                                                function($join)
                                                {
                                                    $join->on('products.id', '=', 'product_sold_tbl.productid');
                                                })->orderby('product_sold_tbl.salecount','Desc')
                                                ->take(5)->get();
            foreach($tolp_sold_product_data as $top_products){
                $top_products->image = asset($top_products->image);
            }
            $top_sold_products['bannerdata']    =   $tolp_sold_product_data;
            array_push($bannerdata, $top_sold_products);

            $result         = [
                                'status'=>true,
                                'data'=> $seller,
                                'shop_banner'=>$bannerdata,
                                'message'=>'Seller Retrieved successfully'
                            ];
        }
        catch (\Exception $e)
        {
            $result = ['status'=>false,'message'=> $e->getMessage()];
        }
        return response()->json($result);
    }

    public function CheckFavoriteShop(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(),[
                'shop_id' => 'required|exists:sellers,id',
                'is_favorite' => 'required'
            ]);
            if($validator->fails())
            {
                $result = ['status'=>false,'message'=> $validator->errors()->all()];
            }
            else
            {
                $shop_id            =   $request->shop_id;
                $user        =  auth()->guard('api')->user();
                if($user)
                {
                    $user_id    =   $user->id;
                } else {
                    $user_id    =   0;
                }
                // $user_id            =   auth()->guard('api')->user()->id;

                $check_approved_shop=   Seller::where('id',$shop_id)
                                        ->where('sellers.approval','=','1')->exists();
                if(!$check_approved_shop){
                    throw new Exception("Shop not Approved Yet");
                }
                $favorite       =   $request->is_favorite;

                // dd($favorite);
                $check_exist    =   DB::table('user_fav_shops')
                                    ->where('shop_id',$shop_id)
                                    ->where('user_id',$user_id)->first();
                $dt 			= new \DateTime();
                $datetime		= $dt->format('Y-m-d H:i:s');
                if($check_exist)
                {
                    if($favorite == true)
                    {
                        $result = ['status'=>false,'message'=> "You already added this Shop as your favorite"];
                    }
                    else
                    {
                        UserFavShop::where("id",$check_exist->id)->delete();
                        $result = ['status'=>true,'message'=> "Successfully removed from favorites."];
                    }
                }
                else
                {
                    if($favorite == true)
                    {
                        UserFavShop::insertGetId(["user_id"=>$user_id,"shop_id"=>$shop_id,"created_at"=>$datetime,"updated_at"=>$datetime]);
                        $result = ['status'=>true,'message'=> "Successfully Added to favorites."];
                    }
                    else
                    {
                        $result = ['status'=>false,'message'=> "You already removed from favorites."];
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

}
