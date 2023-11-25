<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categories;
use App\Models\UserPreferredCategory;
use App\Traits\ProductTrait;
use App\Models\Product;
use App\Models\SellerCategories;
use Illuminate\Support\Facades\DB;
use Exception;

class CategoryAPIController extends Controller
{
    use ProductTrait;

    function cmppreferredcat($a, $b) {
        return strcmp($a->is_preferred, $b->is_preferred);
    }


    public function Categorylist()
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
            // if(!$user_id){
            //     throw new Exception("Please Login to Continue");
            // }

            $categories     =   Categories::select('id','name','image_url')
                                ->where('is_active','=','1')
                                ->get();

            foreach($categories as $key=>$cat){
                $product_count              =   Product::where('category_id',$cat->id)
                                                ->leftjoin('sellers','sellers.id','=','products.seller_id')
                                                ->where('sellers.is_active','1')
                                                ->where('products.status','1')->count();
                if($product_count   == 0){
                unset($categories[$key]);
                }
            }
            if(count($categories) == 0){
                throw new Exception("No Categories Found");
            }
            $category_ids = [];
            foreach($categories as $cat){
                array_push($category_ids,$cat->id);
            }
            if(request()->has('preferred') && request()->preferred != ''){
                $categorylist   =   UserPreferredCategory::select('categories.id','categories.name','categories.image_url')
                                    ->leftjoin('categories','categories.id','=','user_preferred_categories.category_id')
                                    ->where('categories.is_active','=','1')->where('user_id',$user_id)
                                    ->whereIn('categories.id',$category_ids)
                                    ->orderby('categories.name','asc')
                                    ->get();
            } else {
                $categorylist   =   Categories::select('id','name','image_url')
                                    // ->leftjoin('user_preferred_categories','user_preferred_categories.category_id','')
                                    ->where('is_active','=','1')
                                    ->whereIn('categories.id',$category_ids)
                                    ->orderby('categories.name','asc')
                                    ->get();
            }
            if(count($categorylist) > 0)
            {
                foreach($categorylist as $key=>$category)
                {
                    $check_preferred            =   DB::table('user_preferred_categories')->where('user_id',$user_id)
                                                    ->where('category_id',$category->id)->first();

                    if($check_preferred)
                    {
                        $is_preferred        =   True;
                    }
                    else
                    {
                        $is_preferred        =   False;
                    }
                    $category->is_preferred     =   $is_preferred;
                    $category->image_url        =   $category->image_url;
                    $product_count              =   Product::where('category_id',$cat->id)
                                                    ->leftjoin('sellers','sellers.id','=','products.seller_id')
                                                    ->where('sellers.is_active','1')
                                                    ->where('products.status','1')->count();
                    $category->product_count    =   $product_count;
                }

                $result         = ['status'=>true,'count'=>count($categorylist),'data'=> $categorylist, 'message'=>'Categories Listed successfully'];
            }
            else{
                $result         = ['status'=>false, 'message'=>'No Categories found'];
            }
        }
        catch (\Exception $e)
        {
            $result = ['status'=>false,'message'=> $e->getMessage()];
        }
        return response()->json($result);
    }

    public function PreferredCategories()
    {
        try {

            $user_id        =   auth()->guard('api')->user()->id;
            if(!$user_id){
                throw new Exception("Please Login to Continue");
            }
            $preferred_cat  =   request()->preferred_categories;

            if (Categories::where('is_active','=','1')->whereIn('id', $preferred_cat)->count() != count($preferred_cat)) {
                throw new Exception("Please Check all the Categories are Correct");
            }
            $category_exists   =    UserPreferredCategory::where('user_id',$user_id)
                                    ->pluck('category_id')->toArray();

            if(count($category_exists) != 0){
                UserPreferredCategory::where('user_id',$user_id)->delete();
            }

            $insertdata         =   [];
            foreach ($preferred_cat as $key => $id) {
                $insertdata[$key]['user_id']        =   $user_id;
                $insertdata[$key]['category_id']    =   $id;
                $insertdata[$key]['created_at']     =   date('Y-m-d H:i:s');
                $insertdata[$key]['updated_at']     =   date('Y-m-d H:i:s');
            }
            $insert = UserPreferredCategory::insert($insertdata);

            if(!$insert){
                throw new Exception("Something Went Wrong Try Again Later");
            }

            $result =   ['status'=>true, 'message'=>"Preferred Category Updated Successfully"];

        } catch (\Exception $e) {
            $result =   ['status'=>false, 'message'=>$e->getMessage()];
        }
        return response()->json($result);
    }

    public function FilterAttributes()
    {
        try
        {
            if(request()->has('category_id') && request()->category_id != '')
            {
                $catogory_id    =   request()->category_id;

                $cat_det        =   Categories::findorfail($catogory_id);

                if(!$cat_det){
                    throw new Exception("Please Provide Correct Category Id");
                }

                $attribute_list             =   [];
                $attribute_list['cat_id']   =   $catogory_id;
                $attribute_list['cat_name'] =   $cat_det->name;
                $attribute_list['filter']   =   $this->SelectAttributeList($catogory_id);
                $result                     =   ["status"=>true, "data"=>$attribute_list];
            }
            else
            {
                throw new Exception("Please Provide Correct Category Id");
            }

        } catch (\Exception $e) {
            $result         =   ["status"=>false, "message"=>$e->getMessage()];
        }
        return response()->json($result);
    }
}
