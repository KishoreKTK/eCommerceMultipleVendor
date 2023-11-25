<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Categories;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SellerCategoryController extends Controller
{

    public function CategoryList(){
        $seller_id      =   Auth::guard('seller')->user()->id;
        $categories     =   DB::table('categories')
                            ->leftjoin(DB::raw('(SELECT
                                    p.category_id,
                                    COUNT(p.id) AS productcount
                                FROM
                                products AS p
                                WHERE p.Seller_id ='.$seller_id.'
                                GROUP BY p.category_id
                            ) AS producttbl'),
                            function($join)
                            {
                                $join->on('categories.id', '=', 'producttbl.category_id');
                            })
                            ->select('categories.*',DB::raw("IFNULL(producttbl.productcount, '0') as ProductCount"))
                            ->where('categories.is_active','=','1')
                            ->whereNull('categories.deleted_at')
                            ->orderBy('categories.is_active','desc')->get();

        $seller_assigned_count = 0;
        foreach($categories as $cat)
        {
            $seller_categories  =   DB::table('seller_categories')->where('seller_id',$seller_id)
                                    ->whereNull('deleted_at')->pluck('category_id')->toArray();
            if (in_array($cat->id, $seller_categories)) {
                $seller_assigned_count  =   $seller_assigned_count+1;
                $cat->seller_cat_status =   DB::table('seller_categories')->where('seller_id',$seller_id)
                                            ->where('category_id',$cat->id)->whereNull('deleted_at')
                                            ->first()->status;
                $cat->seller_cat = true;
            } else {
                $cat->seller_cat = false;
            }
        }

        $my_category     =  DB::table('categories')
                            ->select('categories.*')
                            ->where('categories.uploaded_by',$seller_id)
                            ->latest('categories.id')->get();

        return view('dashboard.seller.categories.categories',compact('categories','my_category','seller_assigned_count'));

    }

    public function store(Request $request)
    {
        $rules = array(
            'name'   =>    'required|unique:categories',
        );

        $request->validate($rules);
        $dt 			= new \DateTime();
		$datetime		= $dt->format('Y-m-d H:i:s');

        $image_url      = 'Categories';
        $path = Storage::disk('s3')->put($image_url, $request->image);
        $uploadedpath = Storage::disk('s3')->url($path);

        // $image_url      = 'Uploads/Categories/';
        // $image_name     = time() . '.'.$request->file('image')->getClientOriginalExtension();
        // // str_replace(' ', '_', $request->file('image')->getClientOriginalName());
        // $request->image->move(public_path($image_url), $image_name);

        $insert_data = [
            'name'          =>  $request->name,
            'image_url'     =>  $uploadedpath,
            'description'   =>  $request->description,
            'remarks'       =>  "New Category Request",
            'is_active'     =>  '2',
            'reason'        =>  $request->reason,
            'uploaded_by'   =>  $request->seller_id,
            'approved_at'   =>  null,
            'created_at'    =>  $datetime,
            'updated_at'    =>  $datetime,
        ];
        Categories::create($insert_data);
        return back()->with('success','Requested Successfully   ');
    }

    public function AssignCategory(){
        $seller_id      =   Auth::guard('seller')->user()->id;
        $cat_id     =   request()->cat_id;
        if(request()->assign == 0)
        {
            $check_exists = DB::table('seller_categories')->where('seller_id',$seller_id)
                            ->where('category_id',$cat_id)->first();
            if(!$check_exists){
                $result =   ['status'=>false,'message'=>'Category Already Removed From Seller'];
            } else{
                $delete_existing =  DB::table('seller_categories')
                                    ->where('seller_id',$seller_id)
                                    ->where('category_id',$cat_id)
                                    ->update(['status'=>'1','updated_at'=>date('Y-m-d H:i:s')]);

                if($delete_existing){
                    $result =   ['status'=>true,'message'=>'Category Remove Request Sent Successfully'];
                } else {
                    $result =   ['status'=>false,'message'=>'Something Went Wrong. Try Again Later'];
                }
            }
        } else {
            $data   =   [];
            $data['seller_id']      =   $seller_id;
            $data['category_id']    =   $cat_id;
            $data['status']         =   '0';
            $data['remarks']        =   'Remarks Added While After Requesting';
            $data['created_at']     =   date('Y-m-d H:i:s');
            $data['updated_at']     =   date('Y-m-d H:i:s');
            $add_new =  DB::table('seller_categories')->insert($data);
            if($add_new){
                $result =   ['status'=>true,'message'=>'Requested Successfully'];
            } else {
                $result =   ['status'=>false,'message'=>'Something Went Wrong. Try Again Later'];
            }
        }
        if($result['status'] == true) {
            return back()->with('success', $result['message']);
        }
        else {
            return back()->with('error',$result['message']);
        }
    }
}
