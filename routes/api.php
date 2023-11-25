<?php

use App\Http\Controllers\Admin\ContactUsController;
use App\Http\Controllers\API\EcommerceAPIController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\CategoryAPIController;
use App\Http\Controllers\API\ProductAPIController;
use App\Http\Controllers\API\SellerAPIController;
use App\Http\Controllers\API\CartAPIController;
use App\Http\Controllers\API\CollectionsAPIController;
use App\Http\Controllers\API\UserAddressController;
use App\Http\Controllers\API\OrderAPIController;
use App\Http\Controllers\OtherModulesController;
use App\Http\Controllers\TestController;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Auth::routes(['verify' => true]);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('login', [UserController::class,'login']);
Route::post('register', [UserController::class,'register']);

Route::post("forget_password",[UserController::class,'ForgetPassword']);
Route::post('ContactUs',[ContactUsController::class,'ContactUsPost']);

// Banners
Route::get("NewBanners",[EcommerceAPIController::class,'NewBanners']);

// Categories
Route::get('categories', [CategoryAPIController::class,'categorylist']);

// Sellers
Route::post('sellers',[SellerAPIController::class,'sellers']);
Route::get('sellers/{id}',[SellerAPIController::class,'sellerdetail']);

// Products
Route::get('products',[ProductAPIController::class,'productlist']);
Route::get('products/{id}',[ProductAPIController::class,'product_detail']);
Route::post('/get-product-option-id',[ProductAPIController::class,'GetProductOptionEntryId']);


Route::middleware('auth:api')->group(function ()
{
    // User
    Route::post('ChangePassword',[UserController::class,'ChangePassword']);
    Route::post('EditProfile',[UserController::class,'EditProfile']);
    Route::get("UserDet",[UserController::class,'UserDet']);

    // DASHBOARD
    // Route::post("SearchAPI",[EcommerceAPIController::class,'SearchAPI']);
    Route::get("Banners",[EcommerceAPIController::class,'Banners']);

    Route::get('filter',[ProductAPIController::class,'ProductFilterOptions']);

    // Sellers

    Route::post('CheckFavoriteShop',[SellerAPIController::class,'CheckFavoriteShop']);

    // Categories
    Route::post('PreferredCategories', [CategoryAPIController::class,'PreferredCategories']);
    Route::get('FilterAttributes',[CategoryAPIController::class,'FilterAttributes']);

    // Products
    Route::post('CheckFavorite',[ProductAPIController::class,'CheckFavorite']);
    Route::post('CheckSaved',[ProductAPIController::class,'CheckSaved']);
    Route::get('SavedProductList',[ProductAPIController::class,'SavedProductList']);
    // Route::get('ProducReview',[ProductAPIController::class,'product_detail']);

    // Carts
    Route::get('CartList',[CartAPIController::class,'CartList']);
    Route::post('AddToCart',[CartAPIController::class,'AddToCart']);
    Route::get('QuantityChanges',[CartAPIController::class,'QuantityChanges']);
    Route::get('RemoveFromCart',[CartAPIController::class,'RemoveFromCart']);
    Route::get('ClearCart',[CartAPIController::class,'ClearCart']);

    // Address
    Route::get('AddressList',[UserAddressController::class,'index']);
    Route::post('AddAddress',[UserAddressController::class,'add']);
    Route::post('EditAddress',[UserAddressController::class,'update']);
    Route::get('DeleteAddress',[UserAddressController::class,'delete']);
    Route::get('DefaultAddress/{id}',[UserAddressController::class,'UpdateDefaultAddress']);

    // City API
    Route::get('uae-city-api',[UserAddressController::class,'uae_city_api']);
    Route::post('checkdeliverable',[UserAddressController::class,'CheckDeliveryCity']);

    // Orders
    Route::get('CheckOut',[OrderAPIController::class,'ProceedtoCheckOut']);
    Route::post('PlaceOrder',[OrderAPIController::class,'PlaceOrder']);
    Route::get('OrderDetails/{id}',[OrderAPIController::class,'ParticularOrderDet']);
    Route::post('get-transaction-id',[OrderAPIController::class,'UpdateTransactionStatus']);
    Route::get('MyOrders',[OrderAPIController::class,'MyOrders']);

    // Collections
    Route::get('MyCollections',[CollectionsAPIController::class,'MyCollections']);
    Route::post('AddCollection',[CollectionsAPIController::class,'AddNewCollection']);
    Route::post('UpdateCollection',[CollectionsAPIController::class,'UpdateCollection']);
    Route::post('RemoveCollection',[CollectionsAPIController::class,'RemoveCollection']);

    Route::get('/logout',[UserController::class,'logout']);
});

Route::get('faq',[OtherModulesController::class,'FAQ_Api']);

Route::get('Collection/{title}',[CollectionsAPIController::class,'ShareCollectionUrl']);

Route::fallback(function(){
    return response()->json(['status'=>false, 'message' => 'Invalid API Please Check!'], 404);
});


Route::post('TestPayment',[TestController::class,'PaymentPost']);

