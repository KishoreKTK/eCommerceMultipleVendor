<?php

namespace App\Http\Controllers\Admin;

use App\Exports\SellerExport;
use App\Http\Controllers\Controller;
use App\Models\Categories;
use Illuminate\Http\Request;
use App\Models\Seller;
use App\Models\SellerCategories;
use App\Models\ShopImage;
use App\Traits\CategoryTrait;
use App\Traits\ProductTrait;
use App\Traits\ShopTrait;
use Carbon\Carbon;
// use DB;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Excel;
class AdminSellerController extends Controller
{
    use ProductTrait,ShopTrait,CategoryTrait;



    // Seller Request Page
    public function SellerRequestPage(){
        $new_sellers = Seller::where('approval','=','0')->get();
        return view('dashboard.admin.seller.SellerRequest',compact('new_sellers'));
    }

    // Requested Seller Detail Page
    public function VerifySellerPage($id){
        $seller_det = DB::table('sellers')->where('sellers.id','=',$id)->where('approval','=','0')
                    ->Join('uae_city_emirates','uae_city_emirates.id','sellers.seller_city')
                    ->select('sellers.*','uae_city_emirates.city as city_name')->first();
        $seller_categories =    SellerCategories::where('seller_id',$id)->leftjoin('categories','categories.id','=','seller_categories.category_id')
                                ->select('categories.name')->get();
        if($seller_det){
            return view('dashboard.admin.seller.SellerRequestVerification',compact('seller_det','seller_categories'));
        }
        else{
            return redirect()->route('admin.sellerrequest')->with('error','No Pending Approval for this Seller ID');
        }
    }

    // Seller Approve/Reject Function
    public function Approval(Request $request)
    {
        if($request->sellermembership == '1'){
            $active_status  =   '1';
        }
        else{
            $active_status  =   '0';
        }
        if(is_null($request->actionremarks)){
            $actionremarks  =   null;
        }else{
            $actionremarks  =   $request->actionremarks;
        }
        $dt 			= new \DateTime();
		$datetime		= $dt->format('Y-m-d H:i:s');
        $update_data    = [
                            'commission'    => $request->commission,
                            'remarks'       => $actionremarks,
                            'approval'      => $request->sellermembership,
                            'is_active'     => $active_status,
                            'updated_at'    => $datetime
                          ];

        $approve        = DB::table('sellers')->where('id',$request->sellerid)->update($update_data);
        if($approve){

            $seller_det = Seller::where('id',$request->sellerid)->first();
            $email  =  $seller_det->selleremail;
            $business_name = $seller_det->seller_full_name_buss;

            if($active_status == '1'){
                $approve_status =   true;
                $approval_msg   =   'Your Seller Registration Request Has Been Approved.';
            } else {
                $approve_status =   false;
                $approval_msg   =   'Your Registration Requests has been Rejected';
            }

            $data   =   [
                            'name'=>$business_name,
                            'to'=>$email,
                            'approved'=>$approve_status,
                            'message'=>$approval_msg,
                            'remarks'=>$actionremarks
                        ];

            Mail::send('emails.request_approval', ["data"=>$data], function($message) use($data) {
                    $message->to($data['to']);
                    $message->from(env('MAIL_FROM_ADDRESS'), env('APP_NAME'));
                    $message->subject('Starling Request Approval');
            });
            return redirect()->route('admin.sellerrequest')->with('success','Action Completed Successfully');
        }
        else{
            return back()->with('error','Something Went Wrong');
        }

    }



    // Seller List Page
    public function SellerList(){
        $seller =   DB::table('sellers')->select('id','mobile','is_featured',
                        'sellername','selleremail','seller_full_name_buss',
                        'is_active','seller_trade_license','seller_trade_exp_dt',
                        'commission','deleted_at')
                        ->where('approval','=','1');

        if(request()->has('status') && request()->status != '')
        {
            $status    =   request()->status;
            if($status != '2'){
                $seller     =   $seller->where('is_active',request()->status)
                                ->whereNull('deleted_at');
            } else {
                $seller     =   $seller->whereNotNull('deleted_at');
            }

        } else {
            $status     =   '';
            $seller     =   $seller->whereNull('deleted_at');
        }
        if(request()->has('keyword') && request()->keyword != ''){
            $keyword    =   request()->keyword;
            $seller     =   $seller->where(function ($q) use ($keyword) {
                            $q->where("seller_full_name_buss","like",'%'.$keyword.'%')
                            ->orWhere("selleremail","like",'%'.$keyword.'%')
                            ->orWhere("mobile","like",'%'.$keyword.'%');
                            });
        } else {
            $keyword    =   '';
        }
        $sellerlist =   $seller->paginate(20);

        return view('dashboard.admin.seller.sellerlist',compact('sellerlist','status','keyword'));
    }

    public function AddSellerPage()
    {
        $categories     =   $this->SelectCategoryList();
        $uae_cities     =   $this->UaeLargestCities();
        // dd($uae_cities);
        return view('dashboard.admin.seller.CreateSeller',compact('categories','uae_cities'));
    }

    // Create New Seller from Admin Panel
    public function CreateNewSeller(Request $request)
    {
       //Validate inputs
       $request->validate([
                    'sellername'=>'required',
                    'selleremail'=>'required|email|unique:sellers'
                ]);
        $dt 			= new \DateTime();
        $datetime		= $dt->format('Y-m-d H:i:s');
        $input          = $request->except('_token','SellerProfile','seller_trade_license',
                            'category_id','seller_banner_images');

        $name               = $request->sellername;
        $sellerimageurl     = 'Sellers/'.str_replace(' ', '_', $name).'/profile';
        $sellerlicense      = 'Sellers/'.str_replace(' ', '_', $name).'/tradelicense';
        $sellerbannerurl     = 'Sellers/'.str_replace(' ', '_', $name).'/banners';

        $path = Storage::disk('s3')->put($sellerimageurl, $request->SellerProfile);
        $profile_path = Storage::disk('s3')->url($path);

        $licensepath = Storage::disk('s3')->put($sellerlicense, $request->seller_trade_license);
        $sellerlicense_path = Storage::disk('s3')->url($licensepath);
        // $sellername         = time() .'_'. str_replace(' ', '_',$request->file('SellerProfile')->getClientOriginalName());
        // $request->SellerProfile->move(public_path($sellerimageurl), $sellername);

        // $tradename = time() .'_'.str_replace(' ', '_',$request->file('seller_trade_license')->getClientOriginalName());
        // $request->seller_trade_license->move(public_path($sellerlicense), $tradename);

        $input['sellerprofile']         =   $profile_path;
        $input['seller_trade_license']  =   $sellerlicense_path;
        $input['password']              =   Hash::make($request->selleremail);
        $input['approval']              =   '1';
        $input['is_active']             =   '1';
        $input['remarks']               =   "Created By Admin";
        $input['created_at']            =   $datetime;
        $input['updated_at']            =   $datetime;

        // print("<pre>");print_r($input);die;
        $sellerid  = DB::table('sellers')->insertGetId($input);

        if( $sellerid ) {

            // Insert Shop Categories
            $seller_cat_data    =   $this->InsertShopCategories($sellerid,request()->category_id);
            SellerCategories::insert($seller_cat_data);

            // Insert Shop Banners
            $banner_images = $this->InsertShopBannerImages($sellerid,request()->seller_banner_images, $sellerbannerurl);
            ShopImage::insert($banner_images);


            $data   = ['to'=>$request->selleremail, 'name'=>$name,'mail'=>encrypt($request->email)];

            Mail::send('emails.NewSellerInvite', ["data"=>$data], function($message) use($data) {
                    $message->to($data['to']);
                    $message->from(env('MAIL_FROM_ADDRESS'), env('APP_NAME'));
                    $message->subject('Welcome to Starling');
            });

            return redirect()->back()->with('success','Success, Notification Mail Sent to Seller');
        } else{
           return redirect()->back()->with('error','Something went Wrong, failed to register');
        }
    }


    // Edit Seller Page
    public function EditSellerPage($id) {
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
        //Validate inputs
        $seller_id  =   $request->seller_id;
        $request->validate([
            'sellername'=>'required',
            'selleremail'=>'required|email|unique:sellers,selleremail,'.$seller_id.',id'
        ]);
        $input      =   $request->except('_token','SellerProfile','seller_trade_license','seller_id');
        // dd(request()->all());
        $name               = $request->sellername;

        $old_data    =   Seller::where('id',$seller_id)->first();

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


    public function UpdateSellerLocation(Request $request)
    {
        //Validate inputs
        $seller_id  =   $request->seller_id;

        $request->validate([
            'latitude'=>'required',
            'longitude'=>'required'
        ]);

        $latitude               = $request->latitude;
        $longitude               = $request->longitude;

        $time          =   date('Y-m-d H:i:s');

        // $update  = DB::table('sellers')->where('id',$seller_id)->update(["latitude"=>$latitude,"longitude"=>$longitude,"updated_at"=>$time]);
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

    public function UpdateSellerCategory(){
        $seller_cat_data    =   $this->InsertShopCategories(request()->seller_id,request()->category_id);
        $update             =   SellerCategories::insert($seller_cat_data);
        if( $update ) {
            return redirect()->back()->with('success','Updated Successfully');
        } else {
            return redirect()->back()->with('error','Something went Wrong, Try Again Later');
        }
    }

    public function SellerCategoryRequestPage(){
        $seller_category_requests =     DB::table('sellers')
                                        ->leftjoin(DB::raw('(SELECT sc.`category_id`, c.`name` as cat_name
                                        ,sc.`seller_id`,sc.`status`,sc.`created_at`
                                        FROM seller_categories AS sc
                                        LEFT JOIN categories AS c
                                        ON sc.category_id = c.id
                                        WHERE c.`deleted_at` IS NULL
                                            ) AS sellercattbl'),
                                        function($join)
                                        {
                                            $join->on('sellers.id', '=', 'sellercattbl.seller_id');
                                        })
                                        // ->leftJoin('seller_categories','seller_categories.seller_id','=','sellers.id')
                                        ->where('sellercattbl.status','!=','2')
                                        ->whereNull('sellers.deleted_at')
                                        ->where('approval','=','1')
                                        ->orderBy('sellercattbl.created_at')
                                        ->select('sellers.id','mobile','sellers.seller_trade_exp_dt',
                                        'sellername','selleremail','seller_full_name_buss',
                                        'sellercattbl.status','sellercattbl.cat_name',
                                        'sellercattbl.category_id','sellercattbl.created_at')->get();

        return view('dashboard.admin.seller.SellerCatRequest', compact('seller_category_requests'));
    }

    public function ApproveSellerCatRequest(){

        $seller_id      =   request()->sellerid;
        $cat_id     =   request()->category_id;
        if(request()->action_status == '1')
        {
            if(request()->approve_status == '1'){
                // Delete
                $action =   DB::table('seller_categories')
                            ->where('seller_id',$seller_id)
                            ->where('category_id',$cat_id)
                            ->delete();
            } else {
                // Approve
                $action =   DB::table('seller_categories')
                            ->where('seller_id',$seller_id)
                            ->where('category_id',$cat_id)
                            ->update(['status'=>'2','updated_at'=>date('Y-m-d H:i:s')]);
            }

            if($action){
                $result =   ['status'=>true];
            } else {
                $result =   ['status'=>false];
            }
        } else {
            if(request()->approve_status == '1'){
                // Approve
                $action =   DB::table('seller_categories')
                            ->where('seller_id',$seller_id)
                            ->where('category_id',$cat_id)
                            ->update(['status'=>'2','updated_at'=>date('Y-m-d H:i:s')]);
            } else {
                // Delete
                $action =   DB::table('seller_categories')
                            ->where('seller_id',$seller_id)
                            ->where('category_id',$cat_id)
                            ->delete();
            }
            if($action){
                $result =   ['status'=>true,'message'=>'Category Assigned Successfully'];
            } else {
                $result =   ['status'=>false,'message'=>'Something Went Wrong. Try Again Later'];
            }
        }
        if($result['status'] == true) {
            return back()->with('success', 'Action Completed Successfully');
        }
        else {
            return back()->with('error',"Something Went Wrong. Try again Later");
        }
    }


    // Activate / Inactivate Seller
    public function ChangeSellerStatus(Request $request){
        $dt 			= new \DateTime();
		$datetime		= $dt->format('Y-m-d H:i:s');
        $update_data    = [
                            'is_active'     => $request->active_status,
                            'updated_at'    => $datetime
                          ];
                        //   print("<pre>");print_r($request->all());die;
        $approve        = DB::table('sellers')->where('id',$request->sellerid)->update($update_data);
        if($approve){
            return back()->with('success','Action Completed Successfully');
        }
        else{
            return back()->with('error','Something Went Wrong');
        }
    }

    // Update Seller Category
    public function UpdateSellerCat(){
        $dt 			= new \DateTime();
		$datetime		= $dt->format('Y-m-d H:i:s');
        $sellerid       =   request()->seller_id;
        $seller_categories  =   request()->category_id;
        $seller_cat_data    =   [];

        // dd(request()->all());
        // if(SellerCategories::where('seller_id',$sellerid)->exists()){

        // }
        // if($seller_id)
        foreach($seller_categories as $catid){
            $seller_cat_data['seller_id']   =   $sellerid;
            $seller_cat_data['category_id'] =   $catid;
        }
        $updatesellerid = SellerCategories::insert($seller_cat_data);

                        //   print("<pre>");print_r($request->all());die;
        // $approve        = DB::table('sellers')->where('id',$request->sellerid)->update($update_data);
        if($updatesellerid){
            return back()->with('success','Action Completed Successfully');
        }
        else{
            return back()->with('error','Something Went Wrong');
        }
    }

    // View Seller Detail
    public function SellerDet($id)
    {
        $seller_details         =   $this->sellerdetails($id);
        // dd($seller_details);
        $seller_det             =   $seller_details['seller_det'];
        $product_list           =   $seller_details['product_list'];
        $categories             =   $seller_details['categories'];
        $seller_banners         =   $seller_details['seller_banners'];
        $seller_transactions    =   $seller_details['seller_transactions'];
        $seller_categories      =   $seller_details['seller_categories'];
        return view('dashboard.admin.seller.ViewSeller',compact('seller_det','product_list',
        'seller_categories','seller_transactions','categories','seller_banners'));
    }


    // Featured Sellers
    public function featuredsellers(){
        $dt 			=   new \DateTime();
		$datetime		=   $dt->format('Y-m-d H:i:s');
        $update_data    =   [
                                'is_featured'       => request()->is_featured,
                                'updated_at'        => $datetime
                            ];
        $approve        =   DB::table('sellers')
                            ->where('id',request()->seller_id)->update($update_data);
        if($approve){
            return back()->with('success','Action Completed Successfully');
        }
        else{
            return back()->with('error','Something Went Wrong');
        }
    }

    // Delete Seller
    public function DeleteSeller(){
        $dt 			=   new \DateTime();
		$datetime		=   $dt->format('Y-m-d H:i:s');
        $update_data    =   [
                                'deleted_at'        => $datetime
                            ];
        $approve        =   DB::table('sellers')
                            ->where('id',request()->sellerid)->update($update_data);
        if($approve){
            DB::table('seller_categories')->where('seller_id',request()->sellerid)->update(['deleted_at'=>date('Y-m-d H:i:s')]);
            return back()->with('success','Action Completed Successfully');
        }
        else{
            return back()->with('error','Something Went Wrong');
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
        $update  = DB::table('sellers')->where('id',request()->seller_id)->update($input);
        if( $update ) {
            return redirect()->back()->with('success','Updated Successfully');
        } else {
            return redirect()->back()->with('error','Something went Wrong, Try Again Later');
        }
    }


    // Export Seller Report
    public function exportSellers(){
        DB::statement(DB::raw('set @rownum=0'));
        $seller     =   Seller::select(
                        DB::raw("(@rownum:=@rownum + 1) AS sno"),
                        DB::raw("IFNULL(seller_full_name_buss, '-') as seller_full_name_buss"),
                        'sellername','selleremail','mobile',
                        'sellerabout','seller_buss_type',
                        DB::raw("IFNULL(seller_trade_license, '-') as seller_trade_license"),
                        DB::raw("IFNULL(seller_trade_exp_dt, '-') as seller_trade_exp_dt"),
                        DB::raw("
                            (
                                CASE
                                    WHEN is_active='0' THEN 'Inactive'
                                    WHEN is_active='1' THEN 'Active'
                                    ELSE '-'
                                END
                        ) AS is_active"),
                        DB::raw("
                            (
                                CASE
                                    WHEN approval='1' THEN 'Approved'
                                    WHEN approval='2' THEN 'Rejected'
                                    ELSE '-'
                                END
                        ) AS approval"),'remarks','created_at')
                        ->where('approval','=','1')->where('is_active','!=','2');

        if(request()->has('status') && request()->status != ''){
            $seller     =   $seller->where('is_active',request()->status);
        }

        if(request()->has('keyword') && request()->keyword != ''){
            $keyword    =   request()->keyword;
            $seller     =   $seller->where(function ($q) use ($keyword) {
                            $q->where("sellername","like",'%'.$keyword.'%')
                            ->orWhere("selleremail","like",'%'.$keyword.'%');
                            });
        }
        $sellerlist =   $seller->get();

        foreach($sellerlist as $sellers){
            $sellers->created_at  =   Carbon::parse($sellers->created_at)->toFormattedDateString();
        }

        return Excel::download(new SellerExport($sellerlist), 'SellerReport.xlsx');
    }

}
