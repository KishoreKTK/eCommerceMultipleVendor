<?php

namespace App\Http\Controllers;

use App\Models\Banners;
use App\Models\ContactUs;
use App\Models\Content;
use App\Models\FAQ;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDO;

class OtherModulesController extends Controller
{
    public function GetFAQPage(){
        $faqlist    = FAQ::where('status','1')->get();
        return view('dashboard.admin.settings.faq',compact('faqlist'));
    }

    public function PostFAQ(){
        $data = request()->all();
        $insert = FAQ::create($data);
        if($insert == true){
            return redirect()->back()->with('success',"FAQ added Successfully");
        }else{
            return redirect()->back()->with('error',"Something Went Wrong Try Again Later");
        }
    }

    public function GetContentPage(){
        $contentlist = Content::where('status','1')->get();
        return view('dashboard.admin.settings.contents',compact('contentlist'));
    }

    public function PostContent(){
        $data   = request()->all();
        // dd($data);
        $slug   = trim(Str::lower(str_replace(' ', '-',request()->title)));
        $check_exists = Content::where('slug',$slug)->exists();
        if(!$check_exists){
            $data['slug'] = $slug;
            $insert = Content::create($data);
            if($insert == true){
                $result = ['status'=>true, 'message'=>"Inserted Succssfully"];
            }else{
                $result = ['status'=>false, 'message'=>"Somthing Went Wrong Try Again Later"];
            }
        } else{
            $result = ['status'=>false, 'message'=>"Content Already Exists"];
        }
        if($result['status'] == true){
            return redirect()->back()->with('success',$result['message']);
        }else{
            return redirect()->back()->with('error',$result['message']);
        }
    }

    public function UpdateContent(){
        // dd(request()->all());
        $update = DB::table('contents')->where('id',request()->content_id)->update(['content'=>request()->content,'updated_at'=>date('Y-m-d H:i:s')]);
        if($update){
            return redirect()->back()->with('success','Updated Sucessfully');
        }else{
            return redirect()->back()->with('error','Something Went Wrong Try Again Later');
        }
    }

    public function DeleteContent(){
        $delete = Content::destroy(request()->content_id);
        if($delete)
            return redirect()->back()->with('success','Deleted Successfully');
        else
            return redirect()->back()->with('error','Something Went Wrong Try Again Later');
    }

    public function ViewContent($title){
        $check_slug =   Content::where('slug',$title)->first();
        if($check_slug){
            return view('dashboard.admin.settings.ViewContents')->with(['content'=>$check_slug->content,'title'=>$check_slug->title ]);
        }
        else{
            return abort(404);
        }
    }

    public function FAQ_Api()
    {
        try {
            $faqlist    = FAQ::select('question','answer')->get();
            $result     = ['status'=>true,'data'=>$faqlist];
        } catch (\Throwable $th) {
            $result     = ['status'=>false,'message'=>$th->getMessage()];
        }
        return view('dashboard.admin.settings.ViewFAQ',compact('result'));

        return response()->json($result);
    }

    public function contactus(){
        $contactuslist = ContactUs::latest()->paginate(25);
        return view('dashboard.admin.settings.contact_us',compact('contactuslist'));
    }

    public function ReplyContactUs(){
        $update =   ContactUs::where('id',request()->contact_id)->update(['reply'=>request()->reply,'replied_at'=>date('Y-m-d H:i:s')]);
        if($update){
            return redirect()->back()->with('success','Reply Mail Send Sucessfully');
        }else{
            return redirect()->back()->with('error','Something Went Wrong Try Again Later');
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
                                ->select('products.id','products.name','sellers.seller_full_name_buss')
                                ->leftjoin('sellers','sellers.id','products.seller_id')
                                ->where('sellers.is_active','1')
                                ->where('products.status','1')->get();
                foreach($products as $key=>$prod){
                    $data[$prod->id] =   $prod->name.' - '.$prod->seller_full_name_buss;
                }
            } else if($banner_type  ==  2){     //  Products
                $sellers   =   DB::table('sellers')->where('is_active','1')->get();
                foreach($sellers as $key=>$seller){
                    $data[$seller->id] =   $seller->seller_full_name_buss;
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

        // dd("am i coming");
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
        $banners        =   Banners::all();
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
                    $banner->banner_image       =   asset('assets/images/starling_logo.png');
                    $banner->active             =   1;
                    $banner->banner_type_name   =   'Web Link';
            }

            unset($banners[$key]['created_at']);
            unset($banners[$key]['updated_at']);
        }
        return $banners;
    }

}
