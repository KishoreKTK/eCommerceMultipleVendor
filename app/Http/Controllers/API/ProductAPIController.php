<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Models\Seller;
use App\Models\Categories;
use App\Models\UserFavourites;
use App\Models\UserPreferredCategory;
use App\Models\UserSavedProducts;
use App\Traits\ProductTrait;
use Illuminate\Support\Carbon;
use Exception;
use PDO;

class ProductAPIController extends Controller
{
    use ProductTrait;

    public function productlist()
    {
        try
        {
            $availproductids    =   [];
            $filtered_productid =   [];

            $checkproducts = DB::table('products')->where('products.status','1')->get();

            foreach($checkproducts as $key=>$prod){
                $product_options    =   DB::table('product_attributes')->where('product_id',$prod->id)->count();
                $product_spec       =   DB::table('product_specifications')->where('product_id',$prod->id)->count();
                $product_combo      =   DB::table('product_price_combo')->where('product_id',$prod->id)->count();

                if($product_options == 0 || $product_spec == 0 || $product_combo == 0){
                    unset($checkproducts[$key]);
                }

                $seller_to_show = $this->CheckSellertoShow($prod->seller_id);
                if($seller_to_show == true){
                    unset($checkproducts[$key]);
                }
            }

            foreach($checkproducts as $pdt) {
                array_push($availproductids,$pdt->id);
            }

            if(request()->filled('filter'))
            {
                $filter             =   json_decode(request()->filter);
                if(count($filter)!= 0)
                {
                    foreach ($filter as $key => $value)
                    {
                        $filter_type        =   $value->filter_type;
                        $filter_option      =   $value->filter_options;
                        if($filter_type == '1'){
                            $product_ids    =   DB::table('product_attributes')
                                                ->where('sub_attr_id',$filter_option)
                                                ->pluck('product_id');
                            array_push($filtered_productid, $product_ids);
                        }
                        else{
                            $product_ids            =   DB::table('product_stocks')->where('price_type','1');
                            if (str_contains($filter_option, '-')) {
                                $filter_values  =   explode('-',$filter_option);
                                $product_ids->whereBetween('product_price',$filter_values);
                            } else {
                                $filter_value   =   str_replace('>', '', $filter_option);
                                $product_ids->where('product_price','>',$filter_value);
                            }
                            $filterd_ids     =    $product_ids->pluck('product_id');
                            array_push($filtered_productid, $filterd_ids);
                        }
                    }
                    array_unique($filtered_productid);
                    if(count($filtered_productid) == 0){
                        throw new Exception("No Products found under this filter");
                    }
                }
                else {
                    throw new Exception("Please Check the Exception");
                }
            }

            $productlist    =   DB::table('products')
                                ->leftjoin('sellers','products.seller_id','=','sellers.id')
                                ->leftjoin('product_stocks','products.id','=','product_stocks.product_id')
                                ->leftJoin('categories','products.category_id','=','categories.id')
                                ->where('products.status','1')
                                ->where('sellers.is_active','1')
                                ->where('categories.is_active','1')
                                ->where('product_stocks.price_type','1')
                                ->whereIn('products.id',$availproductids)
                                ->select('products.id','products.image','products.name','products.category_id','products.main_price_type',
                                'categories.name as category_name','products.seller_id','sellers.seller_full_name_buss as shopname',
                                'product_stocks.product_price','products.is_featured',
                                DB::raw("
                                (
                                    CASE
                                        WHEN products.main_price_type='1' THEN 'standard'
                                        WHEN products.main_price_type='2' THEN 'custom'
                                        ELSE '-'
                                    END
                                ) AS main_price_type"),'products.created_at');


            // if filter has values - Not for this Starling Project
            if(count($filtered_productid) > 0){
                $productlist =  $productlist->whereIn('products.id', $filtered_productid);
            }

            // Featured Products - Not for this Starling Project
            if(request()->has('is_featured') && request()->is_featured == 1){
                $productlist =  $productlist->where('products.is_featured','=','1');
            }

            // Products Based on Category
            if(request()->has('category_id') && request()->category_id != ''){
                $cate_id = request()->category_id;
                if(!Categories::where('is_active','1')->whereNull('deleted_at')->find($cate_id)){
                    throw new Exception("Please Check Category Id");
                }
                $productlist =  $productlist->where('products.category_id',$cate_id);
            }

            // Products Based on Seller
            if(request()->has('seller_id') && request()->seller_id != ''){
                $seller_id = request()->seller_id;
                $check_seller = Seller::where('sellers.approval','=','1')->whereNull('sellers.deleted_at')
                ->where('sellers.is_active','=','1')->find($seller_id);
                if(!$check_seller){
                    throw new Exception("Please Check Seller Id");
                }
                $productlist =  $productlist->where('products.seller_id',$seller_id);
            }

            // New Products Based on User Preffered Category. if Null then Lastest first
            if(request()->has('newproduct') && request()->newproduct == 1){
                $user        =  auth()->guard('api')->user();
                if($user)
                {
                    $user_id    =   $user->id;
                } else {
                    $user_id    =   0;
                }
                // $user_id                =   auth()->guard('api')->user()->id;
                $user_preffered_cat     =   UserPreferredCategory::where('user_id',$user_id)->count();
                if($user_preffered_cat > 0){
                    $productlist =  $productlist
                                    ->join(DB::raw('(SELECT
                                        category_id
                                    FROM
                                        user_preferred_categories
                                    WHERE user_id = '.$user_id.') as preferred_cat'),function($join){
                                        $join->on('preferred_cat.category_id','=','products.category_id');
                                    })->orderby('products.created_at','desc');
                                    // ->join('user_preferred_categories',
                                    // 'products.category_id','=',
                                    // 'user_preferred_categories.category_id')

                } else{
                    $productlist =  $productlist->orderby('products.created_at','desc')->take('30');
                }
            }

            // Search Keyword Based Filter
            if(request()->has('keywords') && request()->keywords != ''){
                $productlist =  $productlist->Where("products.name","like",'%'.request()->keywords.'%');
            }

            // Sort Values
            if(request()->has('sort_val') && request()->sort_val != '')
            {
                // 1 - popular (Based on Max Orers Placed); 2- newest;
                // 3- price high to low; 4-price lowertohigher
                $sort_val = request()->sort_val;
                if($sort_val == 1){
                    $productlist =  $productlist->leftjoin(DB::raw('(SELECT
                                            productid,
                                            SUM(prod_qty) AS salecount
                                        FROM
                                            order_vendors AS OV
                                        GROUP BY productid
                                    ) AS product_sold_tbl'),
                                    function($join)
                                    {
                                        $join->on('products.id', '=', 'product_sold_tbl.productid');
                                    })->addSelect('salecount')->orderby('product_sold_tbl.salecount','Desc');
                } elseif($sort_val == 2){
                    $productlist =  $productlist->orderby('products.created_at','desc');
                                                // ->addSelect('products.created_at');
                } elseif($sort_val == 3){
                    $productlist =  $productlist->orderby('product_stocks.product_price','Desc');
                } elseif($sort_val == 4){
                    $productlist =  $productlist->orderby('product_stocks.product_price','Asc');
                } else{
                    throw new Exception("Plase Check Provided Sort Id");
                }
            }

            $productlist     =  $productlist->get();

            if(count($productlist) > 0)
            {
                foreach($productlist as $key=>$product)
                {
                    $product->image     =   asset($product->image);
                }

                $result =   [
                                'status'=>true,
                                'count'=>count($productlist),
                                'data'=> $productlist,
                                'message'=>'Products Listed successfully'
                            ];
            }
            else
            {
                $result = ['status'=>false, 'message'=>'No Products found'];
            }
        }
        catch (\Exception $e)
        {
            $result = ['status'=>false,'message'=> $e->getMessage()];
        }
        return response()->json($result);
    }

    public function product_detail($id)
    {
        try
        {
            $product    = Product::join('categories','products.category_id','=','categories.id')
                            ->join('sellers','products.seller_id','=','sellers.id')
                            ->join('product_stocks','products.id','=','product_stocks.product_id')
                            ->select('products.id','products.image','products.name','products.category_id',
                                'categories.name as category_name','products.seller_id',
                                'sellers.seller_full_name_buss as sellername','products.description',
                                'products.is_featured','products.created_at','products.short_bio',
                                'products.shipping_det','products.processing_time',
                                DB::raw("
                                (
                                    CASE
                                        WHEN products.main_price_type='1' THEN 'standard'
                                        WHEN products.main_price_type='2' THEN 'custom'
                                        ELSE '-'
                                    END
                                ) AS main_price_type"),
                                'product_stocks.product_entry as entry_id',
                                'products.starndard_price as product_price','products.total_qty',
                                'products.min_order_qty')
                            ->where('product_stocks.price_type','1')
                            ->where('products.id','=',$id)->first();
            if($product)
            {
                $product->like_count    =   DB::table('user_favourites')->where('product_id',$id)->count();
                $user        =  auth()->guard('api')->user();
                if($user)
                {
                    $user_id    =   $user->id;
                } else {
                    $user_id    =   0;
                }
                // $user_id                =   auth()->guard('api')->user()->id;

                $user_saved             =   DB::table('user_saved_products')->where('user_id',$user_id)
                                            ->where('product_id',$id)->first();
                $user_liked             =   DB::table('user_favourites')->where('user_id',$user_id)
                                            ->where('product_id',$id)->first();

                if($user_saved){
                    $product->user_saved  = true;
                }else{
                    $product->user_saved  = false;
                }
                if($user_liked){
                    $product->user_fav  = true;
                }else{
                    $product->user_fav  = false;
                }

                $product->image         = asset($product->image);
                $product_banner_images  = [];
                $product_banner_images[]= $product->image;

                $product_images         = DB::table('product_images')->select('image_urls')->where('product_id','=',$id)->get();
                foreach($product_images as $images){
                    $product_banner_images[]  = asset($images->image_urls);
                }

                $product->product_banner_images = $product_banner_images;

                $option_listing_det         =   $this->productoptionslisting($id);
                $product->stock             =   $option_listing_det['checK_stock'];
                $product->product_options   =   $option_listing_det['attribute'];
                $product->entry_id          =   $option_listing_det['product_entry_id'];;
                $product->product_price     =   $option_listing_det['product_price'];;
                $product->total_qty         =   $option_listing_det['quantities'];;
                $product->min_order_qty     =   $option_listing_det['min_order_qty'];
                $product->specification     =   DB::table('product_specifications')->where('product_id',$id)
                                                ->select('specification','value')->get();

                $result         = ['status'=>true,'data'=> $product, 'message'=>'Products Retrieved successfully'];
            }
            else
            {
                throw new Exception("Please Check the Product Id");
            }
        }
        catch (\Exception $e)
        {
            $result = ['status'=>false,'message'=> $e->getMessage()];
        }
        return response()->json($result);
    }

    public function CheckFavorite(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(),[
                'product_id' => 'required|exists:products,id',
                'is_favorite' => 'required'
            ]);
            if($validator->fails())
            {
                $result = ['status'=>false,'message'=> $validator->errors()->all()];
            }
            else
            {
                $product_id     =   $request->product_id;
                $user        =  auth()->guard('api')->user();
                if($user)
                {
                    $user_id    =   $user->id;
                } else {
                    $user_id    =   0;
                }
                // $user_id        =   auth()->guard('api')->user()->id;
                $favorite       =   $request->is_favorite;
                $check_exist    =   DB::table('user_favourites')
                                    ->where('product_id',$product_id)
                                    ->where('user_id',$user_id)->first();
                $dt 			= new \DateTime();
                $datetime		= $dt->format('Y-m-d H:i:s');
                if($check_exist)
                {
                    if($favorite==1)
                    {
                        $result = ['status'=>false,'message'=> "You already added this Product as your favorite"];
                    }
                    else
                    {
                        UserFavourites::where("id",$check_exist->id)->delete();
                        $result = ['status'=>true,'message'=> "Successfully removed from your favorites."];
                    }
                }
                else
                {
                    if($favorite==1)
                    {
                       UserFavourites::insertGetId(["user_id"=>$user_id,"product_id"=>$product_id,"created_at"=>$datetime,"updated_at"=>$datetime]);
                       $result = ['status'=>true,'message'=> "Successfully Added to your favorites."];
                    }
                    else
                    {
                        $result = ['status'=>false,'message'=> "You already removed this Product from your favorites."];
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

    public function CheckSaved(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(),[
                'product_id' => 'required|exists:products,id',
                'is_saved' => 'required'
            ]);
            if($validator->fails())
            {
                $result = ['status'=>false,'message'=> $validator->errors()->all()];
            }
            else
            {
                $product_id     =   $request->product_id;
                $user        =  auth()->guard('api')->user();
                if($user)
                {
                    $user_id    =   $user->id;
                } else {
                    $user_id    =   0;
                }
                // $user_id        =   auth()->guard('api')->user()->id;
                $saved          =   $request->is_saved;
                $check_exist    =   DB::table('user_saved_products')
                                    ->where('product_id',$product_id)
                                    ->where('user_id',$user_id)->first();
                $dt 			= new \DateTime();
                $datetime		= $dt->format('Y-m-d H:i:s');
                if($check_exist)
                {
                    if($saved==1)
                    {
                        $result = ['status'=>false,'message'=> "You already added this Product as your Saved List"];
                    }
                    else
                    {
                        UserSavedProducts::where("id",$check_exist->id)->delete();
                        $result = ['status'=>true,'message'=> "Successfully removed from your Saved list."];
                    }
                }
                else
                {
                    if($saved==1)
                    {
                        UserSavedProducts::insertGetId(["user_id"=>$user_id,"product_id"=>$product_id,"created_at"=>$datetime,"updated_at"=>$datetime]);
                       $result = ['status'=>true,'message'=> "Successfully Added to your Saved list."];
                    }
                    else
                    {
                        $result = ['status'=>false,'message'=> "You already removed this Product from your Saved Lists."];
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

    public function SavedProductList()
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
            // $user_id        =   auth()->guard('api')->user()->id;
            $product_id     =   DB::table('user_saved_products')
                                ->where('user_id',$user_id)
                                ->pluck('product_id');
            if(count($product_id) > 0)
            {
                $productlist =   DB::table('products')
                                ->leftjoin('sellers','products.seller_id','=','sellers.id')
                                ->leftjoin('product_stocks','products.id','=','product_stocks.product_id')
                                ->leftJoin('categories','products.category_id','=','categories.id')
                                ->where('products.status','1')
                                ->where('sellers.is_active','1')
                                ->select('products.id','products.image','products.name','products.category_id',
                                'categories.name as category_name','products.seller_id','sellers.sellername',
                                'product_stocks.product_price','products.is_featured')
                                ->whereIn('products.id',$product_id)
                                ->get();

                foreach($productlist as $product){
                    $product->image  = asset($product->image);
                }
                $result = ['status'=>true,'count'=>count($productlist),'data'=> $productlist, 'message'=>'Products Listed successfully'];
            }
            else
            {
                $result = ['status'=>false, 'message'=>'No Products found'];
            }
        }
        catch (\Exception $e)
        {
            $result = ['status'=>false,'message'=> $e->getMessage()];
        }
        return response()->json($result);
    }

    public function ProductFilterOptions()
    {
        try {
            $attributes =   DB::table('attributes')->join('sub_attributes','sub_attributes.attr_id','attributes.id')
                            ->select('attr_id','name',DB::raw('GROUP_CONCAT(sub_attributes.id) as sub_attribute_id'),
                                    DB::raw('GROUP_CONCAT(sub_attr_name) as sub_attribute_names'))
                            ->where('attributes.is_active','1')
                            ->where('sub_attributes.custom','0')
                            ->groupby('sub_attributes.attr_id')->get()->toArray();
            foreach ($attributes as $key => $attr) {
                $sub_attributes     =   [];
                $sub_attr_id    =   explode(',',$attr->sub_attribute_id);
                $sub_attr_val   =   explode(',',$attr->sub_attribute_names);
                foreach ($sub_attr_id as $key => $id) {
                    $sub_attributes[$id]    =   $sub_attr_val[$key];
                }
                unset($attr->attr_id);
                unset($attr->sub_attribute_id);
                unset($attr->sub_attribute_names);
                $attr->filter_options       =   $sub_attributes;
                $attr->filter_type          =   '1';
                // 1 - Dynamic Filter From Table; 2 - Static Filter From Table;
            }

            $static_price = [];
            $static_price['name']             =   'Price';
            $static_price['filter_options']   =   ['<1000', '1000-5000', '5000-10000','10000-20000', '>20000'];
            $static_price['filter_type']      =   '2';

            array_push($attributes, $static_price);
            $result = ['status'=>true,'count'=>count($attributes),'data'=> $attributes, 'message'=>'Products Filter List'];

        }
        catch (\Exception $e)
        {
            $result = ['status'=>false,'message'=> $e->getMessage()];
        }
        return response()->json($result);

    }

    public function GetProductOptionEntryId()
    {
        try
        {
            $option_ids         =   request()->option_ids;
            $opt_ids            =   [];
            $attr_ids           =   [];
            $price_combo_ids    =   [];
            $custom_names       =   [];
            $output             =   [];
            $combooutput        =   [];
            $attribute          =   [];
            $avail_attr_id      =   [];
            $avail_option_id    =   [];
            $avail_opt_name     =   [];
            $first_combo_sub_attr_id    =   [];
            $first_combo_customnames    =   [];
            $loop = 0;
            $first_option_id            =   '';
            foreach($option_ids as $e=>$optn)
            {
                $loop = $loop + 1;
                if($loop == 1){
                    $first_option_id     = $optn['attr_id'];
                }

                $sub_attr_id        =   $optn['option_id'];
                $attr_id            =   $optn['attr_id'];
                array_push($opt_ids, $sub_attr_id);
                array_push($attr_ids, $attr_id);
                array_push($custom_names, $optn['option_name']);
                $price_comb_entry_ids   =   DB::table('product_price_combo')
                                            ->leftJoin('product_stocks','product_stocks.product_entry',
                                            'product_price_combo.product_entry_id')
                                            ->leftJoin('sub_attributes','sub_attributes.id','product_price_combo.sub_attr_id')
                                            ->where('product_price_combo.product_id',request()->product_id)
                                            ->where('product_price_combo.sub_attr_id',$sub_attr_id)
                                            ->where('product_price_combo.custom_values',$optn['option_name'])
                                            ->select(
                                                'product_price_combo.id','product_price_combo.product_entry_id','product_price_combo.attribute_id',
                                                'product_price_combo.sub_attr_id','product_price_combo.custom_values',
                                                'product_stocks.quantities','product_stocks.min_order_qty',
                                                'product_stocks.product_price','sub_attributes.sub_attr_name'
                                            )->get();

                foreach($price_comb_entry_ids as $entryid)
                {
                    array_push($price_combo_ids, $entryid->product_entry_id);

                    $available_options  =   DB::table('product_price_combo')
                                            ->leftJoin('product_stocks','product_stocks.product_entry',
                                            'product_price_combo.product_entry_id')
                                            ->leftJoin('sub_attributes','sub_attributes.id','product_price_combo.sub_attr_id')
                                            ->where('product_price_combo.product_entry_id',$entryid->product_entry_id)
                                            ->select(
                                                'product_price_combo.id','product_price_combo.product_entry_id','product_price_combo.attribute_id',
                                                'product_price_combo.sub_attr_id','product_price_combo.custom_values',
                                                'product_stocks.quantities','product_stocks.min_order_qty',
                                                'product_stocks.product_price','sub_attributes.sub_attr_name')
                                            ->get();
                        foreach($available_options as $e=>$combination)
                        {
                            $data = [];
                            if($combination->sub_attr_id != $sub_attr_id){

                                array_push($avail_attr_id,$combination->attribute_id);
                                array_push($avail_option_id,$combination->sub_attr_id);
                                array_push($avail_opt_name,$combination->custom_values);

                                $data['attribute_id']          =   $combination->attribute_id;
                                if($combination->custom_values != $optn['option_name'])
                                $data['option_id']          =   $combination->sub_attr_id;

                                $data['option_name']    =   $combination->custom_values;
                                $data['custom']   =   '1';
                                $data['product_entry_id']   =   $combination->product_entry_id;
                                $data['min_order_qty']      =   $combination->min_order_qty;
                                $data['quantities']         =   $combination->quantities;
                                $data['product_price']      =   $combination->product_price;

                                array_push($combooutput, $data);
                            }

                        }
                }
            }

            $unique_avail_attr_ids      =   array_unique($avail_attr_id);
            $unique_avail_option_names  =   array_unique($avail_opt_name);
            $unique_price_combo_id      =   array_unique($price_combo_ids);

            $attributelist              =   $this->ProductOptionList(request()->product_id);

            foreach($unique_price_combo_id as $priceid)
            {
                $get_exact_combo = DB::table('product_price_combo')
                                    ->leftJoin('product_stocks','product_stocks.product_entry',
                                    'product_price_combo.product_entry_id')
                                    ->leftJoin('sub_attributes','sub_attributes.id','product_price_combo.sub_attr_id')
                                    ->where('product_price_combo.product_entry_id',$priceid)
                                    ->select(
                                        'product_price_combo.id','product_price_combo.product_entry_id',
                                        DB::raw("GROUP_CONCAT(DISTINCT(attribute_id)) as attribute_id,
                                        GROUP_CONCAT(DISTINCT(sub_attr_id)) as sub_attr_id,
                                        GROUP_CONCAT(DISTINCT(custom_values)) as custom_values"),
                                        'product_stocks.quantities','product_stocks.min_order_qty',
                                        'product_stocks.product_price','sub_attributes.sub_attr_name')
                                    ->groupBy('product_price_combo.product_entry_id')
                                    ->first();

                $combocustom_values     =   explode(",",$get_exact_combo->custom_values);
                if(count($option_ids) == count($attributelist)){
                    $diff                   =   array_diff($combocustom_values, $custom_names);
                    if(count($diff) == 0){
                        $output['product_entry_id']   =   $get_exact_combo->product_entry_id;
                        $output['min_order_qty']      =   $get_exact_combo->min_order_qty;
                        $output['quantities']         =   $get_exact_combo->quantities;
                        $output['product_price']      =   $get_exact_combo->product_price;
                    }
                } else{
                    $first_combo_sub_attr_id    =   explode(",",$get_exact_combo->sub_attr_id);
                    $first_combo_customnames    =   explode(",",$get_exact_combo->custom_values);
                    $output['product_entry_id'] =   $get_exact_combo->product_entry_id;
                    $output['min_order_qty']    =   $get_exact_combo->min_order_qty;
                    $output['quantities']       =   $get_exact_combo->quantities;
                    $output['product_price']    =   $get_exact_combo->product_price;
                }

            }



            foreach($attributelist as $key=>$attr)
            {
                $attribute[$key]['attr_id']         =   intval($attr->attribute_id);
                $attribute[$key]['attr_name']       =   $attr->attrname;
                $option_array                       =   [];
                $optionvalues                       =   explode(',',$attr->custom_values);
                foreach($optionvalues as $custom_values)
                {
                    $opt_val['option_id']       =   intval($attr->sub_attr_ids);
                    $opt_val['option_name']     =   trim($custom_values);
                    $opt_val['custom']          =   $attr->custom;

                    if(in_array( $attr->attribute_id, $attr_ids))
                    {
                        if(in_array( trim($custom_values), $custom_names))
                        {
                            $opt_val['selected']            =   true;
                        } else {
                            $opt_val['selected']            =   false;
                        }
                    }
                    else {
                        if(count($option_ids) == count($attributelist)){
                            $opt_val['selected']            =   false;
                        } else{

                            if(in_array( $attr->attribute_id, $first_combo_sub_attr_id))
                            {
                                if(in_array( trim($custom_values), $first_combo_customnames))
                                {
                                    $opt_val['selected']        =   true;
                                } else{
                                    $opt_val['selected']        =   false;
                                }
                            } else {
                                $opt_val['selected']            =   false;
                            }
                        }
                    }

                    if($first_option_id == $attr->attribute_id){
                        $price_combo_available      =   DB::table('product_price_combo')
                                                        ->where('product_id',request()->product_id)
                                                        ->where('sub_attr_id',$attr->sub_attr_ids)
                                                        ->where('custom_values',trim($custom_values))->first();
                        if($price_combo_available) {
                            $opt_val['in_stock']       =    1;
                        } else {
                            $opt_val['in_stock']       =    0;
                        }
                    } else {
                        if(in_array( $attr->attribute_id, $unique_avail_attr_ids))
                        {
                            if(in_array( trim($custom_values), $unique_avail_option_names))
                            {
                                $opt_val['in_stock']        =    1;
                                $checK_stock        =   true;
                            } else{
                                $opt_val['in_stock']        =    0;
                            }
                        } else {
                            $price_combo_available          =   DB::table('product_price_combo')
                                                                ->where('product_id',request()->product_id)
                                                                ->where('sub_attr_id',$attr->sub_attr_ids)
                                                                ->where('custom_values',trim($custom_values))->first();
                            if($price_combo_available){
                                $opt_val['in_stock']        =    1;
                                // $checK_stock                =   true;
                            } else {
                                $opt_val['in_stock']        =    0;
                            }
                        }
                    }
                    array_push($option_array, $opt_val);
                }
                $attribute[$key]['options']        =   $option_array;
            }

            if($output['min_order_qty'] > 0){
                $output['stock']          =   true;
            } else {
                $output['stock']          =   false;
            }

            $output['product_options']   =   $attribute;
            $result =   ['status'=>true,'data'=>$output,'message'=>'Check Price Combo Listed Successfully'];

        } catch (\Throwable $th) {
            $result =   ['status'=>false,'message'=>$th->getMessage()];
        }
        return response()->json($result);
    }

}
