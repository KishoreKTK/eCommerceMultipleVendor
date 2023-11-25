<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use App\Models\SellerCategories;
use App\Models\ShopImage;
use App\Traits\CategoryTrait;
use App\Traits\ProductTrait;
use App\Traits\ShopTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SellerSettingsController extends Controller
{
    use ShopTrait, CategoryTrait, ProductTrait;
    // Seller Details
    public function SellerDet(){
        $seller_id              =   session()->get('seller_id');
        $seller_details         =   $this->sellerdetails($seller_id);
        $seller_det             =   $seller_details['seller_det'];
        $product_list           =   $seller_details['product_list'];
        $categories             =   $seller_details['categories'];
        $seller_banners         =   $seller_details['seller_banners'];
        $seller_transactions    =   $seller_details['seller_transactions'];
        $seller_categories      =   $seller_details['seller_categories'];

        return view('dashboard.seller.viewseller',compact('seller_det','product_list',
        'seller_categories','seller_transactions','categories','seller_banners'));
    }

    //
    // public function SellerSettings()
    // {
    //     $id                     =   session()->get('seller_id');
    //     $seller_details         =   $this->EditSellerContent($id);
    //     $uae_cities             =   $this->UaeLargestCities();
    //     $seller_det             =   $seller_details['seller_det'];
    //     $categories             =   $seller_details['categories'];
    //     $seller_banners         =   $seller_details['seller_banners'];
    //     $seller_shipping_det    =   $seller_details['seller_shipping_det'];
    //     $latitude=isset($seller_det['latitude'])?$seller_det['latitude']:'25.2048';
    //     $longitude=isset($seller_det['longitude'])?$seller_det['longitude']:'55.2708';
    //     if($latitude==Null) $latitude=25.2048;
    //     if($longitude==Null) $longitude=55.2708;

    //     return view('dashboard.seller.settings.setting'
    //         ,compact('seller_det','seller_banners','categories','uae_cities','seller_shipping_det','latitude','longitude'));
    // }
    
    public function SellerSettings()
    {
        $id                     =   session()->get('seller_id');
        $seller_details         =   $this->EditSellerContent($id);
        $uae_cities             =   $this->UaeLargestCities();
        $seller_det             =   $seller_details['seller_det'];
        $categories             =   $seller_details['categories'];
        $seller_banners         =   $seller_details['seller_banners'];
        $seller_shipping_det    =   $seller_details['seller_shipping_det'];
        $latitude=isset($seller_det['latitude'])?$seller_det['latitude']:'25.2048';
        $longitude=isset($seller_det['longitude'])?$seller_det['longitude']:'55.2708';
        if($latitude==Null) $latitude=25.2048;
        if($longitude==Null) $longitude=55.2708;

        return view('dashboard.commonly_used.v2.seller.editSellerdetails'
            ,compact('seller_det','seller_banners','categories','uae_cities','seller_shipping_det','latitude','longitude'));
    }

    public function EditSellerOrderSettingsPage($id) {
        $seller_details         =   $this->EditSellerContent($id);

        $seller_det             =   $seller_details['seller_det'];
        $categories             =   $seller_details['categories'];
        $seller_banners         =   $seller_details['seller_banners'];
        $uae_cities             =   $this->UaeLargestCities();
        $seller_shipping_det    =   $seller_details['seller_shipping_det'];
        $latitude=isset($seller_det['latitude'])?$seller_det['latitude']:'25.2048';
        $longitude=isset($seller_det['longitude'])?$seller_det['longitude']:'55.2708';
        if($latitude==Null) $latitude=25.2048;
        if($longitude==Null) $longitude=55.2708;
        return view('dashboard.commonly_used.v2.seller.editOrderSellerdetails'
            ,compact('seller_det','seller_banners','categories','uae_cities','seller_shipping_det','latitude','longitude'));
    }

    // Update Seller
    public function UpdateSellerDetail(Request $request)
    {

        $seller_id      =   session()->get('seller_id');

        $request->validate([
            'sellername'=>'required',
            'selleremail'=>'required|email|unique:sellers,selleremail,'.$seller_id.',id'
        ]);
        $input      =   $request->except('_token','SellerProfile','seller_trade_license','seller_id');
        // dd(request()->all());

        $name       =   $request->sellername;

        $old_data   =   Seller::where('id',$seller_id)->first();

        if(request()->has('SellerProfile')){
            $old_pic_path = $old_data->sellerprofile;
            if($old_pic_path)
            {
                Storage::disk('s3')->delete($old_pic_path);
            }

            $sellerimageurl     = 'Sellers/'.str_replace(' ', '_', $name).'/profile';
            $path = Storage::disk('s3')->put($sellerimageurl, $request->SellerProfile);
            $profile_path = Storage::disk('s3')->url($path);
            $input['sellerprofile']         =   $profile_path;
        }

        if(request()->has('seller_trade_license')){
            $old_pic_path = $old_data->seller_trade_license;
            if($old_pic_path)
            {
                Storage::disk('s3')->delete($old_pic_path);
            }

            $sellerlicense      = 'Sellers/'.str_replace(' ', '_', $name).'/tradelicense';
            $licensepath = Storage::disk('s3')->put($sellerlicense, $request->seller_trade_license);
            $sellerlicense_path = Storage::disk('s3')->url($licensepath);
            $input['seller_trade_license']  =   $sellerlicense_path;
        }


        $input['updated_at']            =   date('Y-m-d H:i:s');

        $update  = DB::table('sellers')->where('id',$seller_id)->update($input);

        if( $update ) {
            return redirect()->back()->with('success','Updated Successfully');
        } else {
            return redirect()->back()->with('error','Something went Wrong, Try Again Later');
        }
    }

    //update seller location
    public function UpdateSellerLocation(Request $request)
    {
        $seller_id      =   session()->get('seller_id');

        $request->validate([
            'latitude'=>'required',
            'longitude'=>'required'
        ]);

        $latitude           =   $request->latitude;
        $longitude          =   $request->longitude;
        $time               =   date('Y-m-d H:i:s');
        // $update             =   DB::table('sellers')->where('id',$seller_id)->update(["latitude"=>$latitude,"longitude"=>$longitude, 'pickup'=>$request->pickup, "updated_at"=>$time]);
        $update             =   DB::table('sellers')->where('id',$seller_id)->update(["latitude"=>$latitude,"longitude"=>$longitude, 'pickup'=>$request->pickup,
        'pickup_address'=>$request->pickup_address,'pickup_number'=>$request->pickup_number, "updated_at"=>$time]);

        if( $update ) {

            return redirect()->back()->with('success','Updated Successfully');
        } else {
            return redirect()->back()->with('error','Something went Wrong, Try Again Later');
        }
    }

    public function UpdateBannerImage(Request $request)
    {
        $form_type          =   $request->form_type;
        $shop_name          =   Seller::where('id',$request->seller_id)->first()->sellername;
        $sellerbannerurl    =   'Sellers/'.str_replace(' ', '_', $shop_name).'/banners';
        if($form_type == 'new')
        {
            $banner_images = $this->InsertShopBannerImages($request->seller_id,request()->seller_banner_images, $sellerbannerurl);
            $update = ShopImage::insert($banner_images);
        }
        else
        {
            $banner_id      =   $request->banner_id;
            $data           =   [];

            $path           =   Storage::disk('s3')->put($sellerbannerurl, $request->banner_image);
            $banner_path    =   Storage::disk('s3')->url($path);

            $data['image_urls'] = $banner_path;
            $data['updated_at'] = date('Y-m-d H:i:s');

            $update     = ShopImage::where('id',$banner_id)->Update($data);
        }

        if( $update ) {
            return redirect()->back()->with('success','Updated Successfully');
        } else {
            return redirect()->back()->with('error','Something went Wrong, Try Again Later');
        }
    }

    public function UpdateSellerCategory()
    {
        $seller_cat_data    =   $this->InsertShopCategories(request()->seller_id,request()->category_id);
        $update             =   SellerCategories::insert($seller_cat_data);
        if( $update ) {
            return redirect()->back()->with('success','Updated Successfully');
        } else {
            return redirect()->back()->with('error','Something went Wrong, Try Again Later');
        }
    }

    // Update Seller Shipping Details
    public function AddSellerShippingDetails(){
        $input  =   request()->except('_token');
        $input['created_at']    =   date('Y-m-d H:i:s');
        $input['updated_at']    =   date('Y-m-d H:i:s');
        // dd($input);
        $insert = DB::table('seller_shipping_details')->insert($input);
        if( $insert ) {
            return redirect()->back()->with('success','Updated Successfully');
        } else {
            return redirect()->back()->with('error','Something went Wrong, Try Again Later');
        }
    }

    public function DeleteSellerShippingDetails()
    {
        $update = DB::table('seller_shipping_details')->where('id',request()->shipping_id)->delete();
        if( $update ) {
            return redirect()->back()->with('success','Deleted Successfully');
        } else {
            return redirect()->back()->with('error','Something went Wrong, Try Again Later');
        }
    }

    public function UpdateOrderSettings(){
        $input  =   request()->except('_token','seller_id');
        $input['updated_at']    =   date('Y-m-d H:i:s');
        // dd($input);
        $update  = DB::table('sellers')->where('id',request()->seller_id)->update($input);
        if( $update ) {
            return redirect()->back()->with('success','Updated Successfully');
        } else {
            return redirect()->back()->with('error','Something went Wrong, Try Again Later');
        }
    }
}
