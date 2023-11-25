<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\OtherModulesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EcommerceAPIController extends Controller
{
    // Search API
    public function SearchAPI(){
        try {
            $result = ['status'=>true, "message"=>"Search API Listed Successfully"];
        } catch (\Exception $e) {
            $result = ['status'=>false, "message"=>$e->getMessage()];
        }
        return $result;
    }

    // Filter API
    public function FilterAPI(){

    }

    public function Banners(){
        try {
            $data   = [];

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
                                                    ->select('products.id as product_id','products.image','products.name','products.category_id',
                                                    'categories.name as category_name','products.seller_id as shop_id','sellers.sellername as shopname',
                                                    'product_stocks.product_price','products.is_featured')->where('products.is_featured','=','1')
                                                    ->orderby('products.created_at','desc')->take(5)->get();
            foreach($featured_products_data as $featured_prd){
                $featured_prd->image = asset($featured_prd->image);
            }
            $featured_products['bannerdata']    =   $featured_products_data;
            array_push($data, $featured_products);


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
            array_push($data, $top_sold_products);

            $top_selling_shops  =   [];
            $top_selling_shops['bannerid']      =   '3';
            $top_selling_shops['bannertype']    =   'shops';
            $top_selling_shops['title']         =   "Popular Selling Shops";
            $top_selling_shop_data              =   DB::table('sellers')
                                                    ->select("id as shop_id","sellername as shopname","sellerprofile as image", 'sellercount as ordercount')
                                                    ->where('sellers.approval','=','1')
                                                    ->where('sellers.is_active','=','1')
                                                    ->join(DB::raw('(SELECT
                                                        sellerid,
                                                        COUNT(sellerid) AS sellercount
                                                    FROM
                                                        order_vendors AS OV
                                                        GROUP BY sellerid
                                                    ) AS popular_selelr_tbl'),
                                                    function($join)
                                                    {
                                                        $join->on('sellers.id', '=', 'popular_selelr_tbl.sellerid');
                                                    })->orderby('popular_selelr_tbl.sellercount','Desc')
                                                    ->take(5)->get();
            foreach($top_selling_shop_data as $shops){
                $shops->image = asset($shops->image);
            }
            $top_selling_shops['bannerdata']    =   $top_selling_shop_data;
            array_push($data, $top_selling_shops);

            $popular_categories                 =   [];
            $popular_categories['bannerid']     =   '4';
            $popular_categories['bannertype']   =   'categories';
            $popular_categories['title']        =   "Popular Categories";
            $popular_cat_data                   =   DB::table('categories')
                                                    ->leftJoin(DB::raw('(SELECT
                                                        category_id,
                                                        COUNT(category_id) AS category_count
                                                    FROM
                                                        user_preferred_categories
                                                    GROUP BY category_id) as preferred_categories'),
                                                    function($join)
                                                    {
                                                        $join->on('categories.id','=','preferred_categories.category_id');
                                                    })
                                                    ->where('categories.is_active','1')
                                                    ->orderBy('category_count','Desc')
                                                    ->select('categories.id as category_id','categories.name','categories.image_url as image','preferred_categories.category_count as user_preferences')
                                                    ->take(5)->get();
            foreach($popular_cat_data as $cat){
                $cat->image = asset($cat->image);
            }
            $popular_categories['bannerdata']   =   $popular_cat_data;
            array_push($data, $popular_categories);
            dd($data);
            $result = ['status'=>true, "data"=>$data, "message"=>"Banners API Listed Successfully"];
        } catch (\Exception $e) {
            $result = ['status'=>false, "message"=>$e->getMessage()];
        }
        return $result;
    }


    // Common API comes Here.
    public function NewBanners(){
        try {
            $banners    =   OtherModulesController::BannerList();
            $result = ['status'=>true, "data"=>$banners, "message"=>"Banners API Listed Successfully"];
        } catch (\Exception $e) {
            $result = ['status'=>false, "message"=>$e->getMessage()];
        }
        return $result;
    }


}
