<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\UserCollection;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
class CollectionsAPIController extends Controller
{
    public function MyCollections()
    {
        try
        {
            $user_id            =   auth()->guard('api')->user()->id;
            if(!$user_id){
                throw new Exception("Please Login to Continue");
            }
            if(request()->has('collection_id') && request()->collection_id !=''){
                $usercollection =   UserCollection::where('id',request()->collection_id)->get();
                if(count($usercollection) == 0){
                    throw new Exception("Please Check the Collection Id");
                }
            }else{
                $usercollection =   UserCollection::where('user_id',$user_id)->get();
            }
            $result =   ['status'=>true,'data'=>$this->CollectionQuery($usercollection)];
        }
        catch(Exception $e)
        {
            $result =   ['status'=>false,'message'=>$e->getMessage()];
        }
        return response()->json($result);
    }

    public function ShareCollectionUrl($title)
    {
        try
        {
            $usercollection =   UserCollection::where('slug',$title)->get();
            $result =   ['status'=>true,'data'=>$this->CollectionQuery($usercollection)];
        }
        catch(Exception $e)
        {
            $result =   ['status'=>false,'message'=>$e->getMessage()];
        }
        return response()->json($result);
    }

    public function AddNewCollection()
    {
        try {
            $validator  =   Validator::make(request()->all(),[
                                "title"     => "required",
                                "images"    => "required|array",
                            ]);
            if($validator->fails())
            {
                $errors     = json_encode($validator->errors()->all());
                throw new \Exception($errors);
            }
            $user_id            =   auth()->guard('api')->user()->id;
            if(!$user_id){
                throw new Exception("Please Login to Continue");
            }
            $user_name          =   str_replace(' ', '_',auth()->guard('api')->user()->name);
            $title              =   request()->title;
            $slug               =   time().'-'.str_replace(' ','-', $title);
            if(request()->has('description') && request()->description !=''){
                $desc           =   request()->description;
            } else {
                $desc           =   '-';
            }
            $insert_data        =   ['title'=>$title, 'slug'=>$slug, 'user_id'=>$user_id,
                                    'description'=>$desc, 'created_at'=>date('Y-m-d H:i:s'),
                                    'updated_at'=>date('Y-m-d H:i:s')];
            $collection_id      =   DB::table('user_collections')->insertgetId($insert_data);
            if(!$collection_id){
                throw new Exception("Something Went Wrong Try Again Later");
            }
            $coll_images  =   request()->images;
            $img_url = "Uploads/Customers/".$user_id."-".strtolower($user_name)."/Collections/Collection-".$collection_id."/";
            $collection_images  =   [];

            foreach($coll_images as $key=>$images)
            {
                $image_name         = 'img-'.$key.'.'.$images->getClientOriginalExtension();

                // $bannerimage_name     = time()."_".str_replace(' ', '_',$key.'-'.$images->getClientOriginalName());
                $images->move($img_url, $image_name);
                $collection_images[$key]['collection_id']  = $collection_id;
                $collection_images[$key]['image']          = $img_url.$image_name;
                $collection_images[$key]['created_at']     = date('Y-m-d H:i:s');
                $collection_images[$key]['updated_at']     = date('Y-m-d H:i:s');
            }
            $collection_images  =   DB::table('user_collection_images')->insert($collection_images);
            if(!$collection_images){
                throw new Exception("Something Went Wrong In Uploading Images");
            }
            $result = ['status'=>true, 'message'=>"Collections Uploaded Successfully"];
        } catch (\Exception $e) {
            $result = ['status'=>false, 'message'=>$e->getMessage()];
        }
        return response()->json($result);
    }

    public function UpdateCollection()
    {
        try
        {
            $validator  =   Validator::make(request()->all(),[
                                'id'        => 'required|exists:user_collections,id',
                                "images"    => "array",
                            ]);
            if($validator->fails())
            {
                $errors     = json_encode($validator->errors()->all());
                throw new \Exception($errors);
            }
            $collection_id      =   request()->id;
            $user_id            =   auth()->guard('api')->user()->id;
            if(!$user_id){
                throw new Exception("Please Login to Continue");
            }
            $user_name          =   str_replace(' ', '_',auth()->guard('api')->user()->name);
            $update_data        =   [];
            if(request()->has('title') && request()->title != ''){
                $title                  =   request()->title;
                $update_data['title']   =   $title;
                $update_data['slug']    =   time().'-'.str_replace(' ','-', $title);
            }
            if(request()->has('description') && request()->description !=''){
                $update_data['description']     =   request()->description;
            }
            $update_data['updated_at']   = date('Y-m-d H:i:s');

            $update_id          =   DB::table('user_collections')->where('id',$collection_id)->update($update_data);
            if(!$update_id){
                throw new Exception("Something Went Wrong Try Again Later");
            }
            if(request()->has('images') && request()->images != ''){

                $coll_images  =   request()->images;
                $img_url = "Uploads/Customers/".$user_id."-".strtolower($user_name)."/Collections/Collection-".$collection_id."/";
                $collection_images  =   [];
                $image_existing    =   DB::table('user_collection_images')
                                        ->where('collection_id',$collection_id)->get();
                foreach ($image_existing as $key => $image) {
                    if(File::exists($image->image)){
                        File::delete($image->image);
                    }
                }
                DB::table('user_collection_images')
                ->where('collection_id',$collection_id)->delete();

                foreach($coll_images as $key=>$images)
                {
                    $image_name         = 'img-'.$key.'.'.$images->getClientOriginalExtension();
                    $images->move($img_url, $image_name);
                    $collection_images[$key]['collection_id']  = $collection_id;
                    $collection_images[$key]['image']          = $img_url.$image_name;
                    $collection_images[$key]['created_at']     = date('Y-m-d H:i:s');
                    $collection_images[$key]['updated_at']     = date('Y-m-d H:i:s');
                }
                $collection_images  =   DB::table('user_collection_images')->insert($collection_images);
                if(!$collection_images){
                    throw new Exception("Something Went Wrong In Uploading Images");
                }
            }

            $result = ['status'=>true, 'message'=>"Collections Updated Successfully"];
        } catch (\Exception $e) {
            $result = ['status'=>false, 'message'=>$e->getMessage()];
        }
        return response()->json($result);
    }

    public function RemoveCollection()
    {
        try {
            $id = request()->id;
            if(!$id){
                throw new Exception("Please Provide Collection Id to Continue");
            }
            $collection_id = UserCollection::find($id);
            if(!$collection_id){
                throw new Exception("Please Check Collection Id you have provided");
            }
            $user_id    = Auth::guard('api')->user()->id;
            if(!$user_id){
                throw new Exception("Please Login to Continue");
            }
            $deleteCollection = UserCollection::destroy($collection_id);
            if(!$deleteCollection){
                throw new Exception("Something Went Wrong Try Again later");
            }

            $image_existing    =   DB::table('user_collection_images')
                                        ->where('collection_id',$collection_id)->get();
                foreach ($image_existing as $key => $image) {
                    if(File::exists($image->image)){
                        File::delete($image->image);
                    }
                }
            $delete_images =    DB::table('user_collection_images')
                                ->where('collection_id',$collection_id)->delete();
            if(!$delete_images){
                throw new Exception("Something Went Wrong Try Again Later");
            }
            $result = ['status'=> true, 'Message'=> "Collections Deleted Successfully"];
        } catch (\Throwable $th) {
            $result = ['status'=> false, 'Message'=> $th->getMessage()];
        }
        return response()->json($result);
    }

    function CollectionQuery($usercollection){
        $MyCollection   =   [];
        foreach ($usercollection as $key => $collection) {
            $MyCollection[$key]['id']       =   $collection->id;
            $MyCollection[$key]['title']    =   $collection->title;
            $MyCollection[$key]['slug']     =   asset('api/Collection/'.$collection->slug);
            $MyCollection[$key]['describe'] =   $collection->description;
            $collection_imgs    =   DB::table('user_collection_images')
                                    ->where('collection_id',$collection->id)->get();

            foreach ($collection_imgs as $col_image) {
                $MyCollection[$key]['images'][]   = asset($col_image->image);
            }
            $MyCollection[$key]['created_at']   = Carbon::parse($collection->created_at)->toFormattedDateString();
        }
        return $MyCollection;
    }


}
