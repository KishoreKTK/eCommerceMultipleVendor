<?php

namespace App\Traits;
use Illuminate\Support\Facades\DB;
Use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductImage;
use App\Models\Attributes;
use App\Models\AttributeCategory;
use App\Models\SubAttribute;
use App\Models\Seller;
use App\Models\Categories;
use App\Models\ProductStocks;
use App\Models\SellerCategories;
use Exception;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

trait ShopTrait
{

    function UaeLargestCities(){
        $emirates = DB::table('uae_city_emirates')->get();
        // ->where('emirate',1)->get();
        return $emirates;
    }


    function InsertShopCategories($shop_id,$categories){
        $seller_cat_data    =   [];
        foreach($categories as $key=>$catid)
        {
            $seller_cat_data[$key]['seller_id']   =   $shop_id;
            $seller_cat_data[$key]['category_id'] =   $catid;
            $seller_cat_data[$key]['status']      =   '2';
            $seller_cat_data[$key]['remarks']     =   'Remarks Added While Requesting';
            $seller_cat_data[$key]['created_at']  =   date('Y-m-d H:i:s');
            $seller_cat_data[$key]['updated_at']  =   date('Y-m-d H:i:s');
        }
        return $seller_cat_data;
    }


    function InsertShopBannerImages($shop_id,$banner_images, $banner_img_url)
    {
        $banner = [];
        foreach($banner_images as $key=>$images)
        {
            $path = Storage::disk('s3')->put($banner_img_url, $images);
            $banner_path = Storage::disk('s3')->url($path);

            // $bannerimage_name     = str_replace(' ', '_',$key.'_'.$images->getClientOriginalName());
            // $images->move(public_path($banner_img_url), $bannerimage_name);

            $banner[$key]['shop_id'] = $shop_id;
            $banner[$key]['image_urls'] = $banner_path;
            $banner[$key]['created_at'] = $this->currentdatetime();
            $banner[$key]['updated_at'] = $this->currentdatetime();
        }
        return $banner;
    }


    public function EditSellerContent($id){
        Seller::findorfail($id);
        $seller_det         =   Seller::where('sellers.id','=',$id)->where('sellers.approval','=','1')
                                ->Join('uae_city_emirates','uae_city_emirates.id','sellers.seller_city')
                                ->select('sellers.*','uae_city_emirates.city as city_name')
                                ->first();

        $seller_banners     =   DB::table('shop_images')->where('shop_id',$id)->get();

        $categories         =   DB::table('categories')
                                ->where('categories.is_active','1')
                                ->whereNull('categories.deleted_at')
                                ->latest('id')->get();

        foreach($categories as $cat)
        {
            $seller_categories  =   DB::table('seller_categories')->where('seller_id',$id)
                                    ->whereNull('deleted_at')->pluck('category_id')->toArray();
            if (in_array($cat->id, $seller_categories)) {
                $cat->seller_cat_status =   DB::table('seller_categories')->where('seller_id',$id)
                                            ->where('category_id',$cat->id)->whereNull('deleted_at')
                                            ->first()->status;
                $cat->seller_cat = true;
            } else {
                $cat->seller_cat = false;
            }
        }
        $seller_shipping_city       =   DB::table('seller_shipping_details')->whereNull('deleted_at')
                                        ->leftJoin('uae_city_emirates','uae_city_emirates.id',
                                                    'seller_shipping_details.to_city')
                                        ->select('seller_shipping_details.*','uae_city_emirates.city as to_city_name')
                                        ->where('seller_id',$id)->get();
        $result =   [
            'seller_det'=>$seller_det,
            'categories'=>$categories,
            'seller_banners'=>$seller_banners,
            'seller_shipping_det'=>$seller_shipping_city
        ];

        return $result;
    }


    // View Seller Detail
    public function sellerdetails($id)
    {
        // Seller::findorfail($id);
        $seller_det         =   DB::table('sellers')->where('sellers.id','=',$id)->where('sellers.approval','=','1')
                                ->Join('uae_city_emirates','uae_city_emirates.id','sellers.seller_city')
                                ->select('sellers.*','uae_city_emirates.city as city_name')
                                ->first();
        // $seller_det         =   DB::table('sellers')->where('id','=',$id)->where('approval','=','1')->first();
        $product_list       =   DB::table('products')
                                ->select('products.id','products.name','products.image','starndard_price as price',
                                        'total_qty as available_qty','is_featured','categories.name as categoryname',)
                                ->join('categories','products.category_id','=','categories.id')
                                // ->join('product_stocks','product_stocks.product_id','=','products.id')
                                ->where('seller_id',$id)
                                ->where('status','1')
                                ->get();

        $categories         =   $this->SelectCategoryList();

        $seller_banners     =   DB::table('shop_images')->where('shop_id',$id)->get();

        $seller_transactions= ["status"=>false,"Message"=> "No Transactions Yet"];

        $seller_categories  =   SellerCategories::where('seller_id',$id)
                                ->leftjoin('categories','categories.id','=','seller_categories.category_id')
                                ->where('seller_categories.status','2')->select('categories.name')
                                ->whereNull('categories.deleted_at')
                                ->groupby('seller_categories.category_id')->get();

        $seller_shipping_city       =   DB::table('seller_shipping_details')->whereNull('deleted_at')
                                        ->leftJoin('uae_city_emirates','uae_city_emirates.id',
                                                    'seller_shipping_details.to_city')
                                        ->select('seller_shipping_details.*','uae_city_emirates.city as to_city_name')
                                        ->where('seller_id',$id)->get();

        $result =   [
                        'seller_det'=>$seller_det,
                        'product_list'=>$product_list,
                        'categories'=>$categories,
                        'seller_banners'=>$seller_banners,
                        'seller_transactions'=>$seller_transactions,
                        'seller_categories'=>$seller_categories,
                        'seller_shipping_city'=>$seller_shipping_city
                    ];

        return $result;
    }

}
