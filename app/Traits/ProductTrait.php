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

trait ProductTrait
{
//=========================================================================//
//============ Commonly Used Functions & Select Box Queries ===============//
//=========================================================================//

    function currentdatetime(){
        $dt 			=   new \DateTime();
		$datetime		=   $dt->format('Y-m-d H:i:s');
        return $datetime;
    }

    // function generateUniqueNumber($prestr, $table_model_name)
    // {
    //     $dt         = new \DateTime();
    //     $date       = $dt->format('ymd');
    //     $orderObj   = DB::table($table_model_name)->select('product_entry as unique_num')->latest('id')->first();
    //     if ($orderObj) {
    //         $orderNr            =   $orderObj->unique_num;
	// 		$removed1char       =   substr($orderNr,9);
    //         $dateformat         =   str_pad($removed1char + 1, 3, "0", STR_PAD_LEFT);
    //         $uid                =   $date. $dateformat;
    //         $generateOrder_nr   =   $prestr.$uid;
    //     } else {
    //         $generateOrder_nr   =   $prestr.$date . str_pad(1, 3, "0", STR_PAD_LEFT);
    //     }
    //     return $generateOrder_nr;
	// }

    // function SelectCategoryList(){
    //     $select_options  =   DB::table('categories')->select('id','name')
    //                             ->where('is_active','=','1')->get();
    //     return $select_options;
    // }

    function SelectSellerList(){
        $select_options  =   DB::table('sellers')->select('id','sellername','seller_full_name_buss')
                                ->where('is_active','=','1')->where('approval','1')
                                ->whereNull('sellers.deleted_at')->where('seller_trade_exp_dt','>',date('today'))->get();
        return $select_options;
    }

    function SelectAttributeList($category_id){
        $attributes     =   DB::table('attributes')
                            ->select('attributes.id','attributes.name')
                            ->where('is_active','1')->where('attributes.category_id','=',$category_id)->get();

        $select_options =   [];
        foreach($attributes as $key=>$attr){
            $select_options[$key]['id']         =   $attr->id;
            $select_options[$key]['name']       =   $attr->name;
            $select_options[$key]['sub_attr']   =   DB::table('sub_attributes')
                                                    ->where('status','1')
                                                    ->where('attr_id',$attr->id)
                                                    ->where('custom','0')
                                                    ->get();
            $select_options[$key]['custom_attr']=   DB::table('sub_attributes')
                                                    ->where('status','1')
                                                    ->where('attr_id',$attr->id)
                                                    ->where('custom','1')
                                                    ->get();
        }
        return $select_options;
    }

//=========================================================================//
//========================== Attribute Works  =============================//
//=========================================================================//

    function AttributeList(){
        $login_type =   session()->get('login_type');
        $attribute  =   DB::table('attributes')
                        ->select('attributes.id','attributes.category_id','attributes.name','attributes.is_active',
                        'categories.name as cat_name',"SA_table.sub_attr_name")
                        ->leftjoin(DB::raw('(SELECT
                                SA.attr_id, GROUP_CONCAT(SA.sub_attr_name)  AS sub_attr_name
                                FROM sub_attributes AS SA GROUP BY SA.attr_id) AS SA_table'),
                            function($join)
                            {
                                $join->on('attributes.id', '=', 'SA_table.attr_id');
                            })
                        ->leftJoin('categories','attributes.category_id','=','categories.id');
                        // ->leftjoin(DB::raw('(SELECT
                        //         AC.attr_id, GROUP_CONCAT(C.name)  AS categories
                        //     FROM
                        //     attribute_category AS AC
                        //     JOIN categories AS C
                        //         ON C.id = AC.category_id
                        //     GROUP BY AC.attr_id
                        //     ) AS AC_table'),
                        //     function($join)
                        //     {
                        //     $join->on('attributes.id', '=', 'AC_table.attr_id');
                        // });
        if($login_type == 'admin'){
            $attr_list =    $attribute->where('attributes.is_active','!=','2')->get();
        }
        else{
            $attr_list =    $attribute->where('attributes.is_active','=','1')->get();
        }
        // dd($attr_list);
        return $attr_list;
    }

    // Create Attribute & $request Attribute;
    function CreateAttribute($input)
    {
        $login_type     =   session()->get('login_type');
        $insert_data    =   [
                                'name'          => str::lower($input['name']),
                                'created_at'    => $this->currentdatetime(),
                                'updated_at'    => $this->currentdatetime(),
                            ];

        if($login_type == 'admin')
        {
            $insert_data['is_active'] =     "1";
        }
        else
        {
            $insert_data['is_active'] =     "3";
        }

        $attribute_id = Attributes::insertGetId($insert_data);
        if($attribute_id){
            //Attribute Category Insert
            $insert_attribute_categeory = [];
            foreach($input['category_id'] as $key=>$cat_id){
                $insert_attribute_categeory[$key]['attr_id']        = $attribute_id;
                $insert_attribute_categeory[$key]['category_id']    = $cat_id;
                $insert_attribute_categeory[$key]['created_at']     = $this->currentdatetime();
                $insert_attribute_categeory[$key]['updated_at']     = $this->currentdatetime();
            }

            AttributeCategory::insert($insert_attribute_categeory);

            // Sub Category Insert
            $insert_sub_attributes = [];
            if($input['suggesstions'] != null)
            {
                $sub_attr_arr   = explode(',', $input['suggesstions']);
                foreach($sub_attr_arr as $key=>$sub_attr){
                    $insert_sub_attributes[$key]['attr_id']         = $attribute_id;
                    $insert_sub_attributes[$key]['sub_attr_name']   = trim(Str::lower($sub_attr));
                    $insert_sub_attributes[$key]['created_at']      = $this->currentdatetime();
                    $insert_sub_attributes[$key]['updated_at']      = $this->currentdatetime();
                }
            }
            else
            {
                $insert_sub_attributes['attr_id']         = $attribute_id;
                $insert_sub_attributes['sub_attr_name']   = $input['name'];
                $insert_sub_attributes['summary']         = $input['summary'];
                $insert_sub_attributes['custom']          = "1";
                $insert_sub_attributes['created_at']      = $this->currentdatetime();
                $insert_sub_attributes['updated_at']      = $this->currentdatetime();
            }

            // dd($insert_sub_attributes);
            SubAttribute::insert($insert_sub_attributes);

            $result =   ['status'=>true, 'message'=>"New Attribute Inserted Successfully"];
        }
        else{
            $result =   ['status'=>false, 'message'=>"Error in Inserting New Attribute"];
        }
        return $result;
    }

    // Edit Attrubutes & SubAttributes

    // Change Status(Active|Inactive|Suspend) for Attributes & SubAttributes & Approve Attributes

    // Delete Attributes & SubAttributes


//=========================================================================//
//============================ Products Work  =============================//
//=========================================================================//

    function InsertProductBannerImages($product_id,$banner_images, $banner_img_url)
    {
        $product_banner = [];
        foreach($banner_images as $key=>$images)
        {
            $path = Storage::disk('s3')->put($banner_img_url, $images);
            $banner_img_path = Storage::disk('s3')->url($path);
            $product_banner[$key]['product_id'] = $product_id;
            $product_banner[$key]['image_urls'] = $banner_img_path;
            $product_banner[$key]['created_at'] = $this->currentdatetime();
            $product_banner[$key]['updated_at'] = $this->currentdatetime();
        }
        return $product_banner;
    }

    function ProductSpecArrayValues($product_id,$product_specification){
        $product_spec =     [];
        $loop_count     = 0;

        foreach ($product_specification as $key => $value) {
            $loop_count++;
            $product_spec[$loop_count]['product_id']    = $product_id;
            $product_spec[$loop_count]['specification'] = $key;
            $product_spec[$loop_count]['value']         = $value;
            $product_spec[$loop_count]['created_at']    = $this->currentdatetime();
            $product_spec[$loop_count]['updated_at']    = $this->currentdatetime();
        }
        return $product_spec;
    }

    // Add new Product Option function
    function InsertProductAttributes($product_id, $product_attr, $version)
    {
        $product_insert_arr         =   [];
        $loop_count                 =   0;
        $check_product_id_exists    =   DB::table('product_attributes')
                                        ->where('product_id',$product_id)->first();
        // dd($check_product_id_exists);
        if(!$check_product_id_exists){
            $entry_type             =   "new";
       } else {
            $entry_type = "exists";
        }
        if($entry_type == 'exists'){
            foreach($product_attr as $e=>$attr)
            {
                $check_product_id_exists    =   DB::table('product_attributes')->where('product_id',$product_id)
                                                ->where('attribute_id',$attr['attribute_id'])->first();
                if($check_product_id_exists){
                    unset($product_attr[$e]);
                }
            }
        }

        if(count($product_attr) == 0){
            throw new Exception("Option Already Exists. Please Update Values");
        }

        foreach($product_attr as $key=>$attr)
        {
            $loop_count++;
            if(Arr::exists($product_attr[$key], 'custom_values'))
            {
                if (!empty($attr['custom_values']))
                {
                    $product_insert_arr[$loop_count]['product_id']       = $product_id;
                    $product_insert_arr[$loop_count]['attribute_id']     = $attr['attribute_id'];
                    $product_insert_arr[$loop_count]['sub_attr_id']      = $attr['sub_attr_id'];
                    if($version == 'v1'){
                        $custom_values  = $attr['custom_values'];
                        $custom_val_arr = explode(',',$custom_values);    
                    }
                    else {
                        $custom_val_arr = $attr['custom_values'];
                    }
                    $custom_str_arr = [];
                    foreach($custom_val_arr as $val){
                        $str = ucfirst(strtolower(trim($val)));
                        if(!in_array( $str, $custom_str_arr)){
                            $custom_str_arr[] = $str;
                        }
                    }
                    $product_insert_arr[$loop_count]['custom_values']    = implode(',',$custom_str_arr);
                    $product_insert_arr[$loop_count]['created_at']       = $this->currentdatetime();
                    $product_insert_arr[$loop_count]['updated_at']       = $this->currentdatetime();
                }
            }
            else
            {
                if(Arr::exists($product_attr[$key], 'sub_attr_id'))
                {
                    foreach ($attr['sub_attr_id'] as $e => $value)
                    {
                        $loop_count++;
                        $product_insert_arr[$loop_count]['product_id']           = $product_id;
                        $product_insert_arr[$loop_count]['attribute_id']         = $attr['attribute_id'];
                        $product_insert_arr[$loop_count]['sub_attr_id']          = $value;
                        $product_insert_arr[$loop_count]['custom_values']        = null;
                        // $product_insert_arr[$loop_count]['product_entry_id']     = $unique_entry_id;
                        $product_insert_arr[$loop_count]['created_at']           = $this->currentdatetime();
                        $product_insert_arr[$loop_count]['updated_at']           = $this->currentdatetime();
                    }
                }
            }
        }

        return $product_insert_arr;
    }

    function InserProductStock($product_id, $price_stock)
    {
        $unique_entry_id                = $this->generateUniqueNumber("PDE",'product_stocks');
        $product_stock                  = [];
        $product_stock['product_id']    = $product_id;
        $product_stock['product_entry'] = $unique_entry_id;
        $product_stock['min_order_qty'] = $price_stock['min_order_qty'];
        $product_stock['product_price'] = $price_stock['price'];
        $product_stock['quantities']    = $price_stock['available_qty'];
        $product_stock['created_at']    = $this->currentdatetime();
        $product_stock['updated_at']    = $this->currentdatetime();

        return $product_stock;
    }

    function product_det_query($id){
        $detail =   DB::table('products')->leftjoin('categories','products.category_id','=','categories.id')
                    ->leftjoin('sellers','products.seller_id','=','sellers.id')
                    ->leftjoin('product_images','products.id','=','product_images.product_id')
                    ->leftjoin('product_attributes','products.id','=','product_attributes.product_id')
                    ->leftjoin('product_stocks','products.id','=','product_stocks.product_id')
                    ->select('products.*','categories.name as categoryname','sellers.sellername',
                    'product_stocks.product_price','product_stocks.quantities')
                    ->where('products.id','=',$id)
                    ->where('product_stocks.price_type','1')
                    ->groupby('products.id')->first();
        return $detail;
    }
  
    function GetProductDetail($id)
    {
        try
        {
            Product::findorfail($id);
            $product_details            =  $this->product_det_query($id); 
            $product_images             =   DB::table('product_images')->select('image_urls')->where('product_id','=',$id)->get();
            $product_banner_img         =   [];
            $product_banner_img[]       =   $product_details->image;
            foreach ($product_images as $key => $image) {
                $product_banner_img[]   =   $image->image_urls;
            }

            $product_details->like_count    = DB::table('user_favourites')->where('product_id',$id)->count();
            $product_details->save_count    = DB::table('user_saved_products')->where('product_id',$id)->count();

            $total_sold_products            = DB::table('order_vendors')->where('productid',$id)->pluck('prod_qty');
            $sold_count                     = 0;
            foreach ($total_sold_products as $key => $value) {
                $sold_count                 = $sold_count + $value;
            }
            $product_details->order_count = $sold_count;


            $productattributes =    DB::table('product_attributes')->select('attributes.name as attrname'
                                    ,DB::raw('group_concat(sub_attributes.sub_attr_name) as sub_attr_name'),
                                    'sub_attributes.custom','product_attributes.custom_values')
                                    ->join('attributes','product_attributes.attribute_id','=','attributes.id')
                                    ->leftjoin('sub_attributes','product_attributes.sub_attr_id','=','sub_attributes.id')
                                    ->where('product_attributes.product_id','=',$id)
                                    ->groupBy('product_attributes.attribute_id')->get();
            $attribute  = [];
            foreach($productattributes as $attr){
            if($attr->custom == 0){
            // $sub_attr_arr   =   explode(',',$attr->sub_attr_name);
            // $sub_attr_count =   count($sub_attr_arr);
            // if($sub_attr_count > 1){
            //     $attrval =  'Available';
            // }else{
            //     $attrval =  $attr->sub_attr_name;
            // }
            $attrval =  $attr->sub_attr_name;
            }else{
            // $custom_attr_arr   =   explode(',',$attr->custom_values);
            // $custom_attr_count =   count($custom_attr_arr);
            // if($custom_attr_count > 1){
            //     $attrval =  'Custom Values';
            // }else{
            //     $attrval =  $attr->custom_values;
            // }
            $attrval =  $attr->custom_values;
            }
            $attribute[$attr->attrname]  = $attrval;
            }
            // $product->product_attributes = $attribute;
            $product_stocks     =   DB::table('product_stocks')->where('product_id',$id)->get();
            $data = ['product_det'=> $product_details, 'product_images'=> $product_banner_img,
                    'attributes'=>$attribute, 'product_stocks'=>$product_stocks];

            $result =   ['status'=>true, 'data'=>$data];
        } catch (\Exception $th) {
            $result =   ['status'=>false, 'message'=>$th->getMessage()];
        }
        // dd($result);
        return $result;
    }

    function GetCutomPrices($id){
        try
        {
            Product::findorfail($id);
            $stocklist          =   DB::table('product_stocks')
                                    ->select(
                                    'product_stocks.id',
                                    'product_stocks.price_type',
                                    'product_stocks.product_price',
                                    'product_stocks.quantities',
                                    'product_stocks.product_entry',
                                    'PA_table.product_id',
                                    'PA_table.attribute_ids',
                                    'PA_table.sub_attr_ids',
                                    'PA_table.product_entry_id',
                                    'PA_table.custom_values',
                                    'PA_table.sub_attr_names',
                                    'PA_table.custom',
                                    'PA_table.attr_names')
                                    ->leftjoin(DB::raw('(SELECT
                                            PA.`product_id`,
                                            PA.`product_entry_id`,
                                            GROUP_CONCAT(PA.`attribute_id`) AS attribute_ids,
                                            GROUP_CONCAT(PA.`sub_attr_id`) AS sub_attr_ids,
                                            GROUP_CONCAT(IFNULL(PA.`custom_values`, 0)) AS custom_values,
                                            GROUP_CONCAT(SA.`sub_attr_name`) AS sub_attr_names,
                                            GROUP_CONCAT(SA.`custom`) AS custom,
                                            GROUP_CONCAT(DISTINCT(A.`name`)) AS attr_names
                                        FROM
                                            product_attributes AS PA
                                            JOIN sub_attributes AS SA
                                            ON SA.`id` = PA.`sub_attr_id`
                                            JOIN attributes AS A
                                            ON A.`id` = PA.`attribute_id`
                                        WHERE PA.`product_id` = '.$id.'
                                        GROUP BY PA.`product_entry_id`) AS PA_table'),
                                    function($join)
                                    {
                                        $join->on('product_stocks.product_entry', '=', 'PA_table.product_entry_id');
                                    })->where('product_stocks.product_id',$id)->get();

            // print("<pre>");print_r($stocklist);die;

            $product_det        =   Product::where('products.id',$id)
                                    ->select('products.id','product_stocks.product_price','product_stocks.quantities')
                                    ->join('product_stocks','product_stocks.product_id','=','products.id')
                                    ->where('product_stocks.price_type','1')->first();

            $product_attributes     =    DB::table('product_attributes')
                                        ->select('','attributes.name as attrname'
                                        ,DB::raw('group_concat(sub_attributes.sub_attr_name) as sub_attr_name'),
                                        'sub_attributes.custom','product_attributes.custom_values','product_attributes.product_entry_id')
                                        ->join('attributes','product_attributes.attribute_id','=','attributes.id')
                                        ->leftjoin('sub_attributes','product_attributes.sub_attr_id','=','sub_attributes.id')
                                        ->where('product_attributes.product_id','=',$id)
                                        ->groupBy('product_attributes.attribute_id')
                                        ->get();

            $data   =   [
                            'product_stocks'=>$stocklist,
                            'product_det'=>$product_det,
                            'product_attributes'=> $product_attributes
                        ];

            $result =   ['status'=>true, 'data'=>$data];
        } catch (\Exception $th) {
            $result =   ['status'=>false, 'message'=>$th->getMessage()];
        }
        return $result;
    }

    function AddCustomPrice($input)
    {
        try
        {
            $price_combo            =   $input['price_combo'];
            $product_stock          =   $input['product_stock'];
            $product_id             =   $product_stock['product_id'];
            $stock_combo_qty        =   $product_stock['quantities'];
            $total_stock            =   DB::table('products')->where('id',$product_id)->first()->total_qty;
            $exising_combo_stock    =   DB::table('product_stocks')->where('product_id',$product_id)->where('price_type','2')->sum('quantities');
            $remaing_qty            =   $total_stock - $exising_combo_stock;

            if($remaing_qty < $stock_combo_qty){
                throw new Exception('Quantities Out of Stock');
            }
            // Check Combination Exists

            $unique_entry_id        =   $this->generateUniqueNumber("PDE",'product_stocks');
            $product_attributes     =   [];
            foreach ($price_combo as $key => $combo) {
                if(array_key_exists('attribute_id',$combo))
                {
                    if(array_key_exists('custom_values',$combo)){
                        $combo['custom_values']  = $combo['custom_values'];
                    }else{
                        $combo['custom_values']  = null;
                    }
                    // $get_attr_id
                    $combo['product_entry_id']  = $unique_entry_id;
                    $combo['created_at']        = date('Y-m-d h:i:s');
                    $combo['updated_at']        = date('Y-m-d h:i:s');
                    $product_attributes[$key]   = $combo;
                }
            }
            $insert_prod_data = DB::table('product_price_combo')->insert($product_attributes);

            if(!$insert_prod_data){
                throw new Exception("Something Went Wrong in Adding Product Attributes");
            }
            $product_stock['product_entry'] =   $unique_entry_id;
            $product_stock['price_type']    =   '2';
            $product_stock['created_at']    =   date('Y-m-d H:i:s');
            $product_stock['updated_at']    =   date('Y-m-d H:i:s');

            $insert_prod_stock  =   ProductStocks::insert($product_stock);
            if(!$insert_prod_stock){
                throw new Exception("Something Went Wrong in Adding Product Stocks");
            }

            $result =   ['status'=>true, 'message'=>"Custom Price Added Successfully"];
        } catch (\Exception $th) {
            $result =   ['status'=>false, 'message'=>$th->getMessage()];
        }
        return $result;
    }

    function NewProductStocKUpdate($input){
        try
        {
            $product_stock          =   [];
            $product_id             =   0;
            $stock_combo_qty        =   0;
            $stock_combo =  $input['price_combo'];
            foreach($stock_combo as $e=>$cb)
            {
                if(array_key_exists('option_ids',$cb))
                {
                    $product_id = $cb['product_id'];
                    $stock_combo_qty = $stock_combo_qty + $cb['avail_qty'];
                }
            }

            $total_stock            =   DB::table('products')->where('id',$product_id)->first()->total_qty;
            $exising_combo_stock    =   DB::table('product_stocks')->where('product_id',$product_id)->where('price_type','2')->sum('quantities');
            $remaing_qty            =   $total_stock - $exising_combo_stock;

            if($remaing_qty < $stock_combo_qty){
                throw new Exception('Quantities Out of Stock');
            }

            foreach($stock_combo as $e=>$cb)
            {
                if(array_key_exists('option_ids',$cb))
                {
                    $unique_entry_id        =   $this->generateUniqueNumber("PDE",'product_stocks');
                    $product_stock['product_id']    =   $cb['product_id'];
                    $product_stock['min_order_qty'] =   $cb['min_order'];
                    $product_stock['quantities']    =   $cb['avail_qty'];
                    $product_stock['product_price'] =   $cb['price'];
                    $product_stock['product_entry'] =   $unique_entry_id;
                    $product_stock['price_type']    =   '2';
                    $product_stock['created_at']    =   date('Y-m-d H:i:s');
                    $product_stock['updated_at']    =   date('Y-m-d H:i:s');
                    ProductStocks::insert($product_stock);

                    $option_arr     =   explode(',',$cb['option_ids']);
                    $combo_arr      =   explode(' / ',$cb['combo']);
                    foreach($option_arr as $optid)
                    {
                        $option_values  =   DB::table('product_attributes')->where('attribute_id',$optid)
                                            ->where('product_id',$cb['product_id'])->first()->custom_values;
                        $opt_val_arr    =   explode(',',$option_values);
                        foreach($combo_arr as $optval){
                            if (in_array($optval, $opt_val_arr))
                            {
                                $combo['product_entry_id']  = $unique_entry_id;
                                $combo['product_id']        = $cb['product_id'];
                                $combo['attribute_id']      = $optid;
                                $combo['sub_attr_id']       = $optid;
                                $combo['custom_values']     = $optval;
                                $combo['created_at']        = date('Y-m-d h:i:s');
                                $combo['updated_at']        = date('Y-m-d h:i:s');
                                DB::table('product_price_combo')->insert($combo);
                            }
                        }
                    }
                }
            }

            $result =   ['status'=>true, 'message'=>"Custom Price Added Successfully"];
        } catch (\Exception $th) {
            $result =   ['status'=>false, 'message'=>$th->getMessage()];
        }
        return $result;
    }

    function ProductOptionList($product_id){
        $product_attributes     =   DB::table('product_attributes')->select('product_attributes.attribute_id','attributes.name as attrname',
                                    DB::raw('group_concat(product_attributes.sub_attr_id) as sub_attr_ids'),
                                    DB::raw('group_concat(sub_attributes.sub_attr_name) as sub_attr_name'),
                                    'sub_attributes.custom','product_attributes.custom_values')
                                    ->join('attributes','product_attributes.attribute_id','=','attributes.id')
                                    ->leftjoin('sub_attributes','product_attributes.sub_attr_id','=','sub_attributes.id')
                                    ->where('product_attributes.product_id','=',$product_id)
                                    ->groupBy('product_attributes.attribute_id')->get();
        return $product_attributes;
    }

    function combinations($arrays, $i = 0)
    {
        if (!isset($arrays[$i])) {
            return array();
        }
        if ($i == count($arrays) - 1) {
            return $arrays[$i];
        }

        // get combinations from subsequent arrays
        $tmp = $this->combinations($arrays, $i + 1);

        $result = array();

        // concat each array from tmp with each element from $arrays[$i]
        foreach ($arrays[$i] as $v) {
            foreach ($tmp as $t) {
                $result[] = is_array($t) ?
                    array_merge(array($v), $t) :
                    array($v, $t);
            }
        }

        return $result;
    }

    function ProductComboList($attributelist,$product_id)
    {
        $option_arrays      =   [];
        $option_ids =   [];
        foreach ($attributelist as $key => $value) {
            $option_ids[]       =   $value->attribute_id;
            $custom_values      =   explode(',',$value->custom_values);
            $option_arrays[]    =   $custom_values;

        }
        $opt_str    = implode(",",$option_ids);

        $combinations = $this->combinations($option_arrays);
        $existing_combo   =  DB::table('product_price_combo')->where('product_id',$product_id)->groupBy('product_entry_id')->select('product_entry_id',DB::raw('group_concat(custom_values) as combo'))->get();
        if(count($existing_combo) > 0){
            foreach($existing_combo as $data){
                $combo_arr          =   explode(',',$data->combo);
                if(count($option_ids)>1){
                    foreach($combinations as $key=>$avail_combo){
                        if($avail_combo === array_intersect($avail_combo, $combo_arr) && $combo_arr === array_intersect($combo_arr, $avail_combo)) {
                            unset( $combinations[$key] );
                        }
                    }
                } else {
                    unset( $combinations[array_search( $data->combo, $combinations )] );
                }
            }
        }
        return ['combinations'=>$combinations,'option_ids'=>$option_ids];
    }

    public function ProductStockList($id)
    {
        $stock_list          =  DB::table('product_stocks')
                                ->select(
                                'product_stocks.id',
                                'product_stocks.price_type',
                                'product_stocks.product_price',
                                'product_stocks.min_order_qty',
                                'product_stocks.quantities',
                                'product_stocks.product_entry',
                                'PA_table.product_id',
                                'PA_table.attribute_ids',
                                'PA_table.sub_attr_ids',
                                'PA_table.product_entry_id',
                                'PA_table.custom_values',
                                'PA_table.sub_attr_names',
                                'PA_table.custom',
                                'PA_table.attr_names')
                                ->leftjoin(DB::raw('(SELECT
                                        PA.`product_id`,
                                        PA.`product_entry_id`,
                                        GROUP_CONCAT(PA.`attribute_id`) AS attribute_ids,
                                        GROUP_CONCAT(PA.`sub_attr_id`) AS sub_attr_ids,
                                        GROUP_CONCAT(IFNULL(PA.`custom_values`, 0)) AS custom_values,
                                        GROUP_CONCAT(SA.`sub_attr_name`) AS sub_attr_names,
                                        GROUP_CONCAT(SA.`custom`) AS custom,
                                        GROUP_CONCAT(DISTINCT(A.`name`)) AS attr_names
                                    FROM
                                        product_price_combo AS PA
                                        JOIN sub_attributes AS SA
                                        ON SA.`id` = PA.`sub_attr_id`
                                        JOIN attributes AS A
                                        ON A.`id` = PA.`attribute_id`
                                    WHERE PA.`product_id` = '.$id.'
                                    GROUP BY PA.`product_entry_id`) AS PA_table'),
                                function($join)
                                {
                                    $join->on('product_stocks.product_entry', '=', 'PA_table.product_entry_id');
                                })->where('product_stocks.product_id',$id)->where('product_stocks.price_type','!=','1')->get();

        return $stock_list;
    }

    public function CheckSellertoShow($sellerid)
    {
        $data = DB::table('sellers')
                ->select('sellers.id as shop_id','sellers.seller_full_name_buss as shopname',
                'sellers.sellerarea','sellers.sellerprofile as profile',
                'sellers.pickup','sellers.delivery','sellers.latitude','sellers.longitude',
                'sellers.seller_city as seller_city_id',
                'sellers.emirates as emirate_id','sellers.seller_trade_exp_dt')
                ->where('sellers.approval','=','1')
                ->whereNull('sellers.deleted_at')
                ->where('sellers.is_active','=','1')
                ->where('sellers.id',$sellerid)
                ->first();
        $seller_to_show = false;
        if($data == null){
            $seller_to_show = true;
        } else {
            // if Trade License Expired Not Shown
            if($data->seller_trade_exp_dt==Null)
            {
                $seller_to_show = true;
            }
            else{
                if(strtotime($data->seller_trade_exp_dt) < strtotime('now')){
                    $seller_to_show = true;
                }
            }


            // If no Latitude and Longitude Available
            if($data->latitude == null && $data->longitude == null){
                $seller_to_show = true;
            }

            // If no Delivery and Pickup Available Not Shown
            if($data->pickup == 0 && $data->delivery == 0){
                $seller_to_show = true;
            }

            // if Delivery Yes and No Delivery Area Updated Not Shown
            if($data->delivery == 1){
                $check_shipping_locations = DB::table('seller_shipping_details')->where('seller_id',$data->shop_id)->count();
                if($check_shipping_locations == 0){
                    $seller_to_show = true;
                }
            }

            // if No Products Available don't Show
            $check_avail_products = DB::table('products')->where('seller_id',$data->shop_id)
                                    ->where('status','1')->count();
            if($check_avail_products == 0){
                $seller_to_show = true;
            }

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
                $seller_to_show = true;
            }

        }

        return $seller_to_show;
    }

    public function productoptionslisting($id){
        $attributelist          =   $this->ProductOptionList($id);
        $attribute              =   [];
        $checK_stock            =   false;
        $first_product_entry    =   DB::table('product_stocks')->where('product_id',$id)->where('price_type','2')->first();
        $first_entry_id         =   $first_product_entry->product_entry;
        $first_combo            =   DB::table('product_price_combo')
                                    ->where('product_price_combo.product_entry_id',$first_entry_id)
                                    ->select(
                                        'product_price_combo.id','product_price_combo.product_entry_id',
                                        DB::raw("GROUP_CONCAT(DISTINCT(attribute_id)) as attribute_id,
                                        GROUP_CONCAT(DISTINCT(sub_attr_id)) as sub_attr_id,
                                        GROUP_CONCAT(DISTINCT(custom_values)) as custom_values"))
                                    ->groupBy('product_price_combo.product_entry_id')
                                    ->first();
        $first_combo_sub_attr_id=   explode(",",$first_combo->sub_attr_id);
        $first_combo_customnames=   explode(",",$first_combo->custom_values);
        foreach($attributelist as $key=>$attr)
        {
            $attribute[$key]['attr_id']     =   intval($attr->attribute_id);
            $attribute[$key]['attr_name']   =   ucfirst($attr->attrname);
            $option_array                   =   [];
            $optionvalues                   =   explode(',',$attr->custom_values);
            foreach($optionvalues as $custom_values)
            {
                $opt_val['option_id']       =   intval($attr->sub_attr_ids);
                $opt_val['option_name']     =   trim($custom_values);
                $opt_val['custom']          =   $attr->custom;
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

                $price_combo_available      =   DB::table('product_price_combo')
                                                ->where('product_id',$id)
                                                ->where('sub_attr_id',$attr->sub_attr_ids)
                                                ->where('custom_values',trim($custom_values))->first();
                if($price_combo_available) {
                    $opt_val['in_stock']       =    1;
                } else {
                    $opt_val['in_stock']       =    0;
                }

                array_push($option_array, $opt_val);
            }
            $attribute[$key]['options']        =   $option_array;
        }

        $result['attribute']            =   $attribute;
        $result['product_entry_id']     =   $first_product_entry->product_entry;
        if($first_product_entry->quantities >  $first_product_entry->min_order_qty)
        {
            $result['checK_stock']          =   true;
        } else {
            $result['checK_stock']          =   false;
        }

        $result['min_order_qty']        =   $first_product_entry->min_order_qty;
        $result['quantities']           =   $first_product_entry->quantities;
        $result['product_price']        =   $first_product_entry->product_price;

        return $result;
    }


    function featuredproducts(){

        $update_data    =   [
                                'is_featured'       => request()->is_featured,
                                'updated_at'        => $this->currentdatetime()
                            ];
        $approve        =   DB::table('products')
                            ->where('id',request()->product_id)->update($update_data);
        if($approve){
            return back()->with('success','Action Completed Successfully');
        }
        else{
            return back()->with('error','Something Went Wrong');
        }
    }

    function productstatus()
    {
        $update_data    =   [
                                'status'        => request()->active_status,
                                'updated_at'    => $this->currentdatetime()
                            ];
        $approve        =   DB::table('products')
                            ->where('id',request()->product_id)->update($update_data);
        if($approve){
            return back()->with('success','Action Completed Successfully');
        }
        else{
            return back()->with('error','Something Went Wrong');
        }
    }

    public function UpdateAdditionalImage(){
        $product_det = DB::table('products')->where('id',request()->product_id)->first();
        // dd(request()->all());
        // File Upload Path
        $seller             =   DB::table('sellers')->select('sellername')->where('id',$product_det->seller_id)->first();
        $sellername         =   $seller->sellername;
        $product_img        =   'Sellers/'.str_replace(' ', '_', $sellername).'/Products/'.str_replace(' ', '_', $product_det->name);
        $banner_img_url     =   $product_img.'/banner';
        $path               =   Storage::disk('s3')->put($banner_img_url, request()->image);
        $product_path       =   Storage::disk('s3')->url($path);

        if(request()->img_type == "new"){
            $action         =   DB::table('product_images')->insert(['product_id'=>request()->product_id,'image_urls'=>$product_path,'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')]);
        } else {
            $action         =   DB::table('product_images')->where('id',request()->img_id)->update(['image_urls'=>$product_path,'updated_at'=>date('Y-m-d H:i:s')]);
        }
        if($action){
            return back()->with('success','Action Completed Successfully');
        }
        else{
            return back()->with('error','Something Went Wrong');
        }
    }

    public function DeleteProductImage(){
        $action         =   DB::table('product_images')->where('id',request()->img_id)->delete();
        if($action){
            return back()->with('success','Action Completed Successfully');
        }
        else{
            return back()->with('error','Something Went Wrong');
        }
    }


    // V2
    //  Combination of Both Options and Custom Stocks
    public function ProductCutomStock($id)
    {    
        $product_det        =   $this->product_det_query($id);
        $attributelist      =   $this->SelectAttributeList($product_det->category_id);
        $selected_options   =   [];
        $product_attributes =   $this->ProductOptionList($id);

        if(count($product_attributes)>0){
            foreach($product_attributes as $pa){
                array_push($selected_options, $pa->attribute_id);
            }    
        }

        $stock_list         =   $this->ProductStockList($id);
        // $selected_attr      =   $this->ProductOptionList($id);
        $combo_data         =   $this->ProductComboList($product_attributes,$id);
        $combinations       =   $combo_data['combinations'];
        $option_ids         =   $combo_data['option_ids'];
        return view('dashboard.commonly_used.v2.customstockprice',compact('product_det','attributelist','product_attributes','selected_options','stock_list','combinations','option_ids'));
    }

    
    public function InsertOptionsNew(){
        try {
            $product_id         = request()->product_id;
            $product_attr       = request()->product_attr;
            // Product Attribute Data
            $product_attr_insert_data = $this->InsertProductAttributes($product_id,$product_attr, $version='v2');

            ProductAttribute::insert($product_attr_insert_data);
            // if new option added then delete all existing stock combo
            $check_option_exists =  DB::table('product_price_combo')->where('product_id',$product_id)->count();

            if($check_option_exists>0){
                // Delete Data in Stock Table
                DB::table('product_price_combo')->where('product_id',$product_id)->delete();

                // Delete Data in Combo Table
                DB::table('product_stocks')->where('product_id',$product_id)->where('price_type','2')->delete();

                // Check Product if Available in Cart. if Available delete that product
                $product_in_cart_count = DB::table('carts')->where('product_id',$product_id)->count();
                if($product_in_cart_count > 0){
                    DB::table('carts')->where('product_id',$product_id)->delete();
                }
            }
            return back()->with('success','Product Options Updated');
        } catch(\Exception $e){
            return back()->with('error',$e->getMessage());
        }
    }

    public function UpdateStandardPrice(){
        try{
            $update_data    =   request()->except('_token','product_id');
            $product_id     =   request()->product_id;
            $update_data['updated_at']  =   date('Y-m-d H:i:s');
            $update         =   DB::table('product_stocks')->where('price_type','1')->where('product_id',$product_id)->update($update_data);
            if($update){
                $update_det = [
                    'starndard_price'   => request()->product_price,
                    'total_qty' => request()->quantities,
                    'min_order_qty' => request()->min_order_qty,
                    'updated_at'    => date('Y-m-d H:i:s')
                ];
                DB::table('products')->where('id',$product_id)->update($update_det);
            }    
            return back()->with('success','Product Standard Price Updated');
        } catch(\Exception $e){
            return back()->with('error',$e->getMessage());
        }
    }


    // Update Stocks Function
    public function UpdateStockInfo(){
        $entry_id       =   request()->product_entry_id;
        $update_data    =   request()->except('_token','product_entry_id','product_type','product_id');
        $update_data['updated_at']  =   date('Y-m-d H:i:s');

        $update         =   DB::table('product_stocks')->where('product_entry',$entry_id)->update($update_data);

        if($update){
            if(request()->product_type == '1'){
                $update_det = [
                    'starndard_price'   => request()->product_price,
                    'total_qty' => request()->quantities,
                    'min_order_qty' => request()->min_order_qty,
                    'updated_at'    => date('Y-m-d H:i:s')
                ];
                DB::table('products')->where('id',request()->product_id)->update($update_det);
            }
            return back()->with('success','Action Completed Successfully');
        }
        else{
            return back()->with('error','Something Went Wrong');
        }
    }

    // Delete Custom PRoduct Stock
    public function DeleteCustomProductStock(){
        $entry_id       =   request()->product_entry_id;

        $delete         =   DB::table('product_stocks')
                            ->where('product_entry',$entry_id)->delete();
        if($delete){
            $deletecombo         =   DB::table('product_price_combo')->where('product_entry_id',$entry_id)->delete();
            if($deletecombo){
                return back()->with('success','Action Completed Successfully');
            } else {
                return back()->with('error','Something Went Wrong');
            }
        }
        else{
            return back()->with('error','Something Went Wrong');
        }
    }
    

    
    public function EditProduct($id)
    {
        $result         =   $this->GetProductDetail($id);
        if($result['status'] == true){
            $prod_det       =   $result['data']['product_det'];
            $category_id    =   $prod_det->category_id;
            $seller_list    =   DB::table('seller_categories')
                                ->leftJoin('sellers','sellers.id','=','seller_categories.seller_id')
                                ->where('seller_categories.category_id',$category_id)
                                ->where('sellers.approval','1')->where('sellers.is_active','1')
                                ->whereNull('sellers.deleted_at')->groupBy('sellers.id')->get();
            $product_specifications     =   DB::table('product_specifications')->where('product_id',$id)->get();
            $product_images             =   DB::table('product_images')->where('product_id',$id)->get(); 
            return view('dashboard.commonly_used.v2.EditProduct')->with([
                'product_det'   =>  $prod_det,
                'sellerlist'    =>  $seller_list,
                'categorylist'  =>  $this->SelectCategoryList(),
                'product_specifications'=>$product_specifications,
                'product_images'=>$product_images
            ]);
        }
        else{
            return redirect()->back()->with('error',$result['message']);
        }
    }


}
