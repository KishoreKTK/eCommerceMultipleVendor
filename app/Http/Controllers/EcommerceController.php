<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDO;

class EcommerceController extends Controller
{

    public function WebPage()
    {
        return view('website.index');
    }

    public function CheckAuthenticated()
    {
        if( Auth::guard('seller')->check() )
        {
            return redirect()->route('seller.home');
        }
        else
        {
            return redirect()->route('seller.login');
        }
    }

    public function ContactPostForm()
    {
        try
        {
            $input = request()->all();
            $input['created_at']    =   date('Y-m-d H:i:s');
            $input['updated_at']    =   date('Y-m-d H:i:s');
            $result =   ['status'=>true,'message'=>"Thanks you for Contacting Starling"];
        } catch(Exception $e){
            $result =   ['status'=>false, 'message'=>"Something Went Wrong. Try Again Later"];
        }
        return response()->json($result);
    }

    public function TermsnConditions(){
        $title  =   'terms-and-conditions';
        $check_slug =   Content::where('slug',$title)->first();
        if($check_slug){
            return view('website.otherpages')->with(['content'=>$check_slug->content,'title'=>$check_slug->title ]);
        }
        else{
            return abort(404);
        }
    }

    public  function PrivacyPolicy(){
        $title  =   'privacy-policy';
        $check_slug =   Content::where('slug',$title)->first();
        if($check_slug){
            return view('website.otherpages')->with(['content'=>$check_slug->content,'title'=>$check_slug->title ]);
        }
        else{
            return abort(404);
        }
    }

    // View Banners
    public function banners(){
        $banners    =   $this->BannerList();
        return view('dashboard.admin.settings.banners',compact('banners'));
    }

    public function ChangeBannerType(){
        try {
            $banner_type    =   request()->banner_type;
            $data           =   [];
            if($banner_type ==  1){             // Seller
                $products   =   DB::table('products')
                                ->select('products.id','products.name','sellers.sellername')
                                ->leftjoin('sellers','sellers.id','products.seller_id')
                                ->where('sellers.is_active','1')
                                ->where('products.status','1')->get();
                foreach($products as $key=>$prod){
                    $data[$prod->id] =   $prod->name.' - '.$prod->sellername;
                }

            } else if($banner_type  ==  2){     //  Products
                $sellers   =   DB::table('sellers')->where('is_active','1')->get();
                foreach($sellers as $key=>$seller){
                    $data[$seller->id] =   $seller->sellername.' - '.$seller->selleremail;
                }
            } else {                            // Categories
                $categories   =   DB::table('categories')->where('is_active','1')->get();
                foreach($categories as $cat){
                    $data[$cat->id] =   $cat->name;
                }
            }
            $result =   ['status'=>true,'data'=>$data,'message'=>'Listed Succssfully'];
        } catch (\Exception $e) {
            $result =   ['status'=>false,'message'=>$e->getMessage()];
        }
        return response()->json($result);
    }

    public function AddBanners(Request $request)
    {
        $rules = array(
            'title'         => 'required',
            'description'   => 'required',
            'banner_type'   => 'required'
        );
        $request->validate($rules);
        if($request->banner_type != 4)
        {
            if(!$request->filled('sub_banner'))
            {
                return redirect()->back()->with('error',"Please Select Sub Order Id");
            } else {
                $sub_banner_id  =   $request->sub_banner;
                $link           =   null;
            }
        } else {
            if(!$request->filled('link'))
            {
                return redirect()->back()->with('error',"Please Select Link");
            }
            $sub_banner_id  =   null;
            $link           =   $request->link;
        }

        $insert_data = [
            'title'         =>  $request->title,
            'description'   =>  $request->description,
            'banner_type'   =>  $request->banner_type,
            'sub_banner_id' =>  $sub_banner_id,
            'link'          =>  $link,
            'created_at'    =>  date('Y-m-d H:i:s'),
            'updated_at'    =>  date('Y-m-d H:i:s'),
        ];
        $inserted = Banners::create($insert_data);
        if($inserted)
            return back()->with('success','New Banner Added');
        else
            return redirect()->back()->with('error',"Something Went Wrong");
    }

    public function DeleteBanner($id){
        $delete = Banners::destroy($id);
        if($delete)
            return redirect()->back()->with('success','Deleted Successfully');
        else
            return redirect()->back()->with('error','Something Went Wrong Try Again Later');
    }

    public static function BannerList(){
        $banners        =   DB::table('banners')->all();
        foreach($banners as $key=>$banner){
            $banner_type    =   $banner->banner_type;
            $sub_banner_id  =   $banner->sub_banner_id;
            // Products
            if($banner_type == '1'){
                $product        =   DB::table('products')->where('id',$sub_banner_id)
                                    ->where('status','1')->first();
                if($product){
                    $banner->banner_image    =   asset($product->image);
                    $banner->active     =   1;
                } else {
                    $banner->banner_image    =   asset('assets/images/no_image.png');
                    $banner->active     =   0;
                }
                $banner->banner_type_name   =   'Products';
            }
            // Sellers
            else if($banner_type == '2'){
                $seller     =   DB::table('sellers')->where('is_active','1')->where('id',$sub_banner_id)->first();
                if($seller){
                    $banner->banner_image   =   asset($seller->sellerprofile);
                    $banner->active         =   1;
                } else {
                    $banner->banner_image   =   asset('assets/images/no_image.png');
                    $banner->active         =   0;
                }
                $banner->banner_type_name   =   'Sellers';
            } else if($banner_type == '3'){
                $category     =   DB::table('categories')->where('is_active','1')->where('id',$sub_banner_id)->first();
                if($category){
                    $banner->banner_image   =   asset($category->image_url);
                    $banner->active         =   1;
                } else {
                    $banner->banner_image   =   asset('assets/images/no_image.png');
                    $banner->active         =   0;
                }
                $banner->banner_type_name   =   'Categories';
            } else {
                    $banner->banner_image       =   asset('assets/images/logo-horizontal.png');
                    $banner->active             =   1;
                    $banner->banner_type_name   =   'Web Link';
            }

            unset($banners[$key]['created_at']);
            unset($banners[$key]['updated_at']);
        }
        return $banners;
    }

}
