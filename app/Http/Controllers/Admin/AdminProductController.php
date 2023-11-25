<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\API\ProductAPIController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attributes;
use App\Models\Product;
use App\Models\Seller;
use App\Models\Categories;
use App\Models\ProductAttribute;
use App\Models\ProductImage;
use App\Models\ProductStocks;
use App\Models\SubAttribute;
use App\Traits\CategoryTrait;
use App\Traits\ProductTrait;
use Attribute;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PDO;

class AdminProductController extends Controller
{
    use ProductTrait, CategoryTrait;  

    //==========================================================================================//
    // Product Attribute Functions
    //==========================================================================================//


    // Product Attribute List
    public function ProductAttributesList()
    {
        $attributes     =   $this->AttributeList();

        $category_list  =   $this->SelectCategoryList();
        return view('dashboard.admin.products.ProductAttribute',
                    compact('attributes','category_list'));
    }

    // Add Product Attribute
    public function createattributes(Request $request)
    {
        $rules = array(
            'name'          =>      'required',
            'category_id'   =>      'required',
        );
        $request->validate($rules);
        $input              =   $request->all();
        $check_exiting_attr =   DB::table('attributes')->where('name',Str::lower($input['name']))
                                ->where('category_id',$input['category_id'])->first();
        if($check_exiting_attr){
            $result =   ['status'=>false, 'message'=>'Option Already Exists in this Category'];
        } else {
            $login_type     =   session()->get('login_type');
            $insert_data    =   [
                                    'name'          =>  Str::lower($input['name']),
                                    'category_id'   =>  $input['category_id'],
                                    'created_at'    =>  date('Y-m-d H:i:s'),
                                    'updated_at'    =>  date('Y-m-d H:i:s'),
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

            if($attribute_id)
            {
            //     // Sub Category Insert
            //     $insert_sub_attributes = [];
            //     if($input['suggesstions'] != null)
            //     {
            //         $sub_attr_arr   = explode(',', $input['suggesstions']);
            //         foreach($sub_attr_arr as $key=>$sub_attr){
            //             $insert_sub_attributes[$key]['attr_id']         = $attribute_id;
            //             $insert_sub_attributes[$key]['sub_attr_name']   = trim(Str::lower($sub_attr));
            //             $insert_sub_attributes[$key]['created_at']      = $this->currentdatetime();
            //             $insert_sub_attributes[$key]['updated_at']      = $this->currentdatetime();
            //         }
            //     }
            //     else
            //     {
                    $insert_sub_attributes['attr_id']         = $attribute_id;
                    $insert_sub_attributes['sub_attr_name']   = $input['name'];
                    $insert_sub_attributes['summary']         = 'Custom Options';
                    $insert_sub_attributes['custom']          = "1";
                    $insert_sub_attributes['created_at']      = $this->currentdatetime();
                    $insert_sub_attributes['updated_at']      = $this->currentdatetime();
            //     }
            //     // dd($insert_sub_attributes);
                SubAttribute::insert($insert_sub_attributes);
            }
            $result =   ['status'=>true,'message'=>"Option Added Succesfully"];
        }
        if($result['status'] == true)
        {
            return back()->with('success',$result['message']);
        }
        return back()->with('error',$result['message']);
    }


    //==========================================================================================//
    // Product List Page Functions
    //==========================================================================================//

    // Product List
    public function ProductList(){
        $products   =   Product::select('products.*',
                        'categories.name as categoryname','sellers.seller_full_name_buss')
                        ->join('categories','products.category_id','=','categories.id')
                        ->join('sellers','products.seller_id','=','sellers.id')
                        ->where('sellers.approval','=','1')
                        ->whereNull('sellers.deleted_at')
                        ->where('sellers.is_active','=','1')
                        ->orderby('products.id','desc')
                        ->where('status','!=','2')->paginate('25');
        foreach($products as $key=>$prod){
            $product_id = $prod->id;
            $prod->option_count         =   DB::table('product_attributes')->where('product_id',$product_id)->count();
            $prod->spec_count           =   DB::table('product_specifications')->where('product_id',$product_id)->count();
            $prod->product_combo        =   DB::table('product_price_combo')->where('product_id',$product_id)->count();
        }
        return view('dashboard.admin.products.productlist',compact('products'));
    }

    // Add New Product Page
    public function AddProductPage()
    {
        if(request()->has('category_id')){
            $category_id    = request()->category_id;
            Categories::findorfail($category_id);
        }
        else{
            $category_id    = 0;
        }
        if(request()->ajax())
        {
            return view('dashboard.commonly_used.product_form_attribute_section')
                    ->with(['attributelist' =>  $this->SelectAttributeList($category_id)]);
        }
        return view('dashboard.admin.products.AddProduct')
            ->with([
                'sellerlist'    =>  $this->SelectSellerList(),
                'categorylist'  =>  $this->SelectCategoryList(),
                'attributelist' =>  $this->SelectAttributeList($category_id)
            ]);
    }

    // Common function
    public function AdminSellerCategory(){
        try {
            $seller_list    =   DB::table('seller_categories')
            // ->select('sellers.id','sellers.seller_full_name_buss')
                                ->leftJoin('sellers','sellers.id','=','seller_categories.seller_id')
                                ->where('seller_categories.category_id',request()->category_id)
                                ->where('sellers.approval','1')->where('sellers.is_active','1')
                                ->whereNull('sellers.deleted_at')->groupBy('sellers.id')->get();
            // dd($seller_list);
            $attributelist =  $this->SelectAttributeList(request()->category_id);

            // dd($attributelist);
            if(count($seller_list) == 0){
                throw new Exception('No Seller Found Under this Category');
            }
            $result =   ['status'=>true, 'data'=>['seller_list'=>$seller_list,'attribute_list'=>$attributelist], 'message'=>'Sellers Under this Categories Listed Successfully'];
        } catch (\Exception $th) {
            $result =   ['status'=>false,'message'=>$th->getMessage()];
        }
        return response()->json($result);
    }


    //==========================================================================================//
    // Add Product Page Functions
    //==========================================================================================//


    // Create Product Function
    public function CreateProduct(Request $request)
    {
        try
        {
            // dd(request()->all());
            // Insert Table Main Records
            $product_insert_det = $request->except('_token','image','banner','product_spec');
            if($product_insert_det['short_bio'] == null){
                $product_insert_det['short_bio'] = '-';
            } 
            if($product_insert_det['description'] == null){
                $product_insert_det['description'] = '-';
            } 
         
            // File Upload Path
            $seller             =  DB::table('sellers')->select('sellername')->where('id',$request->seller_id)->first();
            $sellername         =  $seller->sellername;
            $product_img        = 'Sellers/'.str_replace(' ', '_', $sellername).'/Products/'.str_replace(' ', '_', $request->name);
            // $image_name         = time() . '.'.$request->file('image')->getClientOriginalExtension();
           
           


            // Move Image to Folder
            // $request->image->move(public_path($product_img), $image_name);

            $path = Storage::disk('s3')->put($product_img, $request->image);
            $product_path = Storage::disk('s3')->url($path);
       
            // Product Table Additional Data
            $product_insert_det['image']        = $product_path;
            $product_insert_det['status']       = '1';
            $product_insert_det['shipping_det'] = 'Varies Based on Seller Location.';
            $product_insert_det['created_at']   = $this->currentdatetime();
            $product_insert_det['updated_at']   = $this->currentdatetime();

            $product_id     = Product::insertGetId($product_insert_det);
            if($product_id)
            {

                // Banner Image Storage Data
                $banner_images      = $request->banner;
                $banner_img_url     = $product_img.'/banner';    
                $product_banner = $this->InsertProductBannerImages($product_id,$banner_images, $banner_img_url);
                ProductImage::insert($product_banner);
                

                // Product Spec Data
                $specification      = $request->product_spec;
                $spec_keys          = array_filter($specification['key']);
                $spec_values        = array_filter($specification['value']);
                $product_spec       = array_combine($spec_keys, $spec_values);

                $product_spec_values = $this->ProductSpecArrayValues($product_id,$product_spec);
                DB::table('product_specifications')->insert($product_spec_values);

                // // Product Attribute Data
                // $product_attr_insert_data = $this->InsertProductAttributes($product_id,$product_attr);
                // ProductAttribute::insert($product_attr_insert_data);


                // Product Stock Data
                $unique_entry_id    = $this->generateUniqueNumber("PDE",'product_stocks');

                $product_stock  =   [];
                $product_stock['product_id']    = $product_id;
                $product_stock['product_entry'] = $unique_entry_id;
                $product_stock['min_order_qty'] = $product_insert_det['min_order_qty'];
                $product_stock['product_price'] = $product_insert_det['starndard_price'];
                $product_stock['quantities']    = $product_insert_det['total_qty'];
                $product_stock['created_at']    = $this->currentdatetime();
                $product_stock['updated_at']    = $this->currentdatetime();

                ProductStocks::insert($product_stock);
            }
            return redirect()->route('admin.ProductCutomStock',[$product_id])->with('success','New Product Added, Please Update Options and Stocks to Complete Product Details');
        }
        catch(\Exception $e){
            return back()->with('error',$e->getMessage());
        }
    }

    // public function EditProduct($id)
    // {
    //     $result         =   $this->GetProductDetail($id);
    //     if($result['status'] == true){
    //         $prod_det       =   $result['data']['product_det'];
    //         $category_id    =   $prod_det->category_id;
    //         $seller_list    =   DB::table('seller_categories')
    //                             ->leftJoin('sellers','sellers.id','=','seller_categories.seller_id')
    //                             ->where('seller_categories.category_id',$category_id)
    //                             ->where('sellers.approval','1')->where('sellers.is_active','1')
    //                             ->whereNull('sellers.deleted_at')->groupBy('sellers.id')->get();
    //         $product_specifications     =   DB::table('product_specifications')->where('product_id',$id)->get();
    //         $product_images             =   DB::table('product_images')->where('product_id',$id)->get(); 
    //         return view('dashboard.commonly_used.v2.EditProduct')->with([
    //             'product_det'   =>  $prod_det,
    //             'sellerlist'    =>  $seller_list,
    //             'categorylist'  =>  $this->SelectCategoryList(),
    //             'product_specifications'=>$product_specifications,
    //             'product_images'=>$product_images
    //         ]);
    //     }
    //     else{
    //         return redirect()->back()->with('error',$result['message']);
    //     }
    // }

    public function UpdateProduct(){
        $update_data = request()->except('_token','product_id');
        $exising_prod = DB::table('products')->where('id',request()->product_id)->first();
        if(request()->has('image')){
            $old_pic_path = $exising_prod->image;
            if($old_pic_path)
            {
                Storage::disk('s3')->delete($old_pic_path);
            }
            $seller             =  DB::table('sellers')->select('sellername')->where('id',request()->seller_id)->first();
            $sellername         =  $seller->sellername;
            $product_img        = 'Sellers/'.str_replace(' ', '_', $sellername).'/Products/'.str_replace(' ', '_', request()->name);
            $path = Storage::disk('s3')->put($product_img, request()->image);
            $product_path = Storage::disk('s3')->url($path);
            $update_data['image']        = $product_path;
        }

        $update_data['updated_at']            =   date('Y-m-d H:i:s');

        $update  = DB::table('products')->where('id',request()->product_id)->update($update_data);

        if( $update ) {
            return redirect()->back()->with('success','Updated Successfully');
        } else {
            return redirect()->back()->with('error','Something went Wrong, Try Again Later');
        }
    }
    //==========================================================================================//
    // Product Details Page Functions
    //==========================================================================================//

    // Product Details
    function ProductDetails($id)
    {
        $result =   $this->GetProductDetail($id);
        if($result['status'] == true)
            return view('dashboard.admin.products.ViewProduct')->with($result['data']);
        else
            return redirect()->back()->with('error',$result['message']);
    }


    //==========================================================================================//
    // Product Stock Functions
    //==========================================================================================//

    // Product Stocks Page
    public function ProductStocksPage($id){
        $product_det        =   DB::table('products')->where('id',$id)->first();
        $stock_list         =   $this->ProductStockList($id);
        $attributelist      =   $this->ProductOptionList($id);
        $combo_data         =   $this->ProductComboList($attributelist,$id);
        $combinations       =   $combo_data['combinations'];
        $option_ids         =   $combo_data['option_ids'];
        return view('dashboard.admin.products.productstocks',compact('product_det','attributelist','stock_list','combinations','option_ids'));
    }


    public function AddProductStock()
    {
        $input  =   request()->all();
        $result =   $this->NewProductStocKUpdate($input);
        if($result['status'] == true)
            return redirect()->back()->with('success',$result['message']);
        else
            return redirect()->back()->with('error',$result['message']);
    }

    // Update price type
    public function UpdatePriceType(){
        $dt 			=   new \DateTime();
		$datetime		=   $dt->format('Y-m-d H:i:s');
        $update_data    =   [
                                'main_price_type'        => request()->main_price_type,
                                'updated_at'    => $datetime
                            ];
        $update        =   DB::table('products')
                            ->where('id',request()->product_id)->update($update_data);
        if($update){
            return back()->with('success','Action Completed Successfully');
        }
        else{
            return back()->with('error','Something Went Wrong');
        }
    }

    // // Update Stocks Function
    // public function UpdateStockInfo(){
    //     $entry_id       =   request()->product_entry_id;
    //     $update_data    =   request()->except('_token','product_entry_id','product_type','product_id');
    //     $update_data['updated_at']  =   date('Y-m-d H:i:s');

    //     $update         =   DB::table('product_stocks')->where('product_entry',$entry_id)->update($update_data);

    //     if($update){
    //         if(request()->product_type == '1'){
    //             $update_det = [
    //                 'starndard_price'   => request()->product_price,
    //                 'total_qty' => request()->quantities,
    //                 'min_order_qty' => request()->min_order_qty,
    //                 'updated_at'    => date('Y-m-d H:i:s')
    //             ];
    //             DB::table('products')->where('id',request()->product_id)->update($update_det);
    //         }
    //         return back()->with('success','Action Completed Successfully');
    //     }
    //     else{
    //         return back()->with('error','Something Went Wrong');
    //     }
    // }

    // // Delete Custom PRoduct Stock
    // public function DeleteCustomProductStock(){
    //     $entry_id       =   request()->product_entry_id;

    //     $delete         =   DB::table('product_stocks')
    //                         ->where('product_entry',$entry_id)->delete();
    //     if($delete){
    //         $deletecombo         =   DB::table('product_price_combo')->where('product_entry_id',$entry_id)->delete();
    //         if($deletecombo){
    //             return back()->with('success','Action Completed Successfully');
    //         } else {
    //             return back()->with('error','Something Went Wrong');
    //         }
    //     }
    //     else{
    //         return back()->with('error','Something Went Wrong');
    //     }
    // }



    //==========================================================================================//
    // Product Options Page Functions
    //==========================================================================================//

    // Product Options Page
    public function ProductOptionsPage($id){
        $product_det        =   DB::table('products')->where('id',$id)->first();

        $attributelist      =   $this->SelectAttributeList($product_det->category_id);

        $product_attributes =   $this->ProductOptionList($id);
        // dd($attributelist);
        $selected_options   =   [];
        foreach($product_attributes as $pa){
            array_push($selected_options, $pa->attribute_id);
        }

        return view('dashboard.admin.products.productoptions',
                compact('product_det','attributelist','product_attributes','selected_options'));
                // ,'product_specifications'));
    }

    // Update Product Options Function
    public function AddProductOptions()
    {
        try {
            $product_id         = request()->product_id;
            $product_attr       = request()->product_attr;
            // Product Attribute Data
            $product_attr_insert_data = $this->InsertProductAttributes($product_id,$product_attr, $version='v1');

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

    public function UpdateProductOption(){
        try {
            $product_id         = request()->product_id;
            $sub_attr_id        = request()->sub_attr_id;
            $new_custom_val     = request()->custom_values;
            $combo_customnames  = implode(",",$new_custom_val);

            // if Existing Option Removed Check for Combo in that Option and Remove the Combo.
            $check_option_exists =  DB::table('product_price_combo')->where('product_id',$product_id)
                                    ->where('attribute_id',$sub_attr_id)->pluck('custom_values')->toArray();

            if(count($check_option_exists)>0){
                $check_exists = array_diff(array_unique($check_option_exists),$new_custom_val);
                if(count($check_exists)>0){
                    $get_combo_id =  DB::table('product_price_combo')->where('product_id',$product_id)
                    ->where('attribute_id',$sub_attr_id)->whereIn('custom_values',$check_exists)->pluck('product_entry_id')->toArray();
                    if($get_combo_id){
                        // Delete Data in Stock Table
                        DB::table('product_price_combo')->whereIn('product_entry_id',$get_combo_id)->delete();

                        // Delete Data in Combo Table
                        DB::table('product_stocks')->whereIn('product_entry',$get_combo_id)->delete();

                        // Check Product if Available in Cart. if Available delete that product
                        $product_in_cart_count = DB::table('carts')->whereIn('product_entry_id',$get_combo_id)->count();
                        if($product_in_cart_count > 0){
                            DB::table('carts')->whereIn('product_entry_id',$get_combo_id)->delete();
                        }
                    }
                }
            }
            ProductAttribute::where('product_id',$product_id)->where('sub_attr_id',$sub_attr_id)
                            ->update(['custom_values'=>$combo_customnames,'updated_at'=>date('Y-m-d H:i:s')]);
            return back()->with('success','Product Options Updated');
        } catch(\Exception $e){
            return back()->with('error',$e->getMessage());
        }
    }

    public function DeleteProductOption(){
        try {
            $product_id         = request()->product_id;
            $sub_attr_id        = request()->sub_attr_id;
            ProductAttribute::where('product_id',$product_id)->where('sub_attr_id',$sub_attr_id)->delete();
            // if existing option deleted then delete all existing stock combo
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
            return back()->with('success','Product Option Deleted');
        } catch(\Exception $e){
            return back()->with('error',$e->getMessage());
        }
    }


    //==========================================================================================//
    // Product Specification Functions
    //==========================================================================================//

    // Product Specification List Page
    public function ProductSpecificationPage($id)
    {
        $product_det                =   DB::table('products')->where('id',$id)->first();
        $product_specifications     =   DB::table('product_specifications')->where('product_id',$id)->get();
        return view('dashboard.admin.products.productspecification',
        compact('product_det','product_specifications'));
    }

    // Add Product Specifications Function
    public function AddProductSpec()
    {
        try {
            $insert_data    =   [
                'product_id'    =>  request()->product_id,
                'specification' =>  request()->specification,
                'value'         =>  request()->value,
                'created_at'    =>  date('Y-m-d H:i:s'),
                'updated_at'    =>  date('Y-m-d H:i:s')
            ];
            DB::table('product_specifications')->insert($insert_data);
            return back()->with('success','Product Specifications Updated');
        } catch(\Exception $e){
            return back()->with('error',$e->getMessage());
        }
    }

    // Update Specification function
    public function UpdateProductSpec()
    {
        $spec_id        =   request()->spec_id;
        $update_data    =   request()->except('_token','spec_id');
        $update_data['updated_at']  =   date('Y-m-d H:i:s');

        $update         =   DB::table('product_specifications')
                            ->where('id',$spec_id)->update($update_data);
        if($update){
            return back()->with('success','Action Completed Successfully');
        }
        else{
            return back()->with('error','Something Went Wrong');
        }
    }

    // DeleteProductSpec
    public function DeleteProductSpec()
    {
        $spec_id        =   request()->spec_id;
        $delete         =   DB::table('product_specifications')
                            ->where('id',$spec_id)->delete();
        if($delete)
        {
            return back()->with('success','Action Completed Successfully');
        }
        else
        {
            return back()->with('error','Something Went Wrong');
        }
    }



    //==========================================================================================//
    // Other Functions
    //==========================================================================================//

    // Filtered Prodcuts
    public function GetFilterOptionsPage()
    {
        $attributes =   DB::table('attributes')->join('sub_attributes','sub_attributes.attr_id','attributes.id')
                        ->select('attr_id','name',DB::raw('GROUP_CONCAT(sub_attributes.id) as sub_attribute_id'),
                                DB::raw('GROUP_CONCAT(sub_attr_name) as sub_attribute_names'),'custom')
                        ->where('attributes.is_active','1')
                        ->groupby('sub_attributes.attr_id')->get();

        $filters    =   [];
        return view('dashboard.admin.products.filters',compact('filters','attributes'));
    }


    function generateUniqueNumber($prestr, $table_model_name)
    {
        $dt         = new \DateTime();
        $date       = $dt->format('ymd');
        $orderObj   = DB::table($table_model_name)->select('product_entry as unique_num')->latest('id')->first();
        if ($orderObj) {
            $orderNr            =   $orderObj->unique_num;
			$removed1char       =   substr($orderNr,9);
            $dateformat         =   str_pad($removed1char + 1, 3, "0", STR_PAD_LEFT);
            $uid                =   $date. $dateformat;
            $generateOrder_nr   =   $prestr.$uid;
        } else {
            $generateOrder_nr   =   $prestr.$date . str_pad(1, 3, "0", STR_PAD_LEFT);
        }
        return $generateOrder_nr;
	}


}
