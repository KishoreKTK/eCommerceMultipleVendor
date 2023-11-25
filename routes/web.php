<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EcommerceAPIController;

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\AdminSellerController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminCustomerController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\API\UserController as APIUserController;
use App\Http\Controllers\EcommerceController;
use App\Http\Controllers\OtherModulesController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\User\UserController;
use App\Models\Admin;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/',[EcommerceController::class,'WebPage'])->name('webpage');
Route::get('terms-and-conditions',[EcommerceController::class,'TermsnConditions'])->name('TermsnConditions');
Route::get('privacy-policy',[EcommerceController::class,'PrivacyPolicy'])->name('PrivacyPolicy');


Route::get('/SellerLoginCheck',[EcommerceController::class,'CheckAuthenticated'])->name('checkloginuser');

Route::get('VerifyEmail',[APIUserController::class,'VerifyEmail']);
Route::get('ResetEmail',[APIUserController::class,'ResetEmail']);
Route::post('UpdatePassword',[APIUserController::class,'UpdatePassword']);

Route::prefix('admin')->name('admin.')->group(function()
{
    Route::middleware(['guest:admin','PreventBackHistory'])->group(function(){
        Route::view('/login','dashboard.admin.login')->name('login');
        Route::post('/check',[AdminController::class,'check'])->name('check');
        Route::view('/getforgetpassword','dashboard.admin.forgetpass')->name('getforgetpassword');
        Route::post('/ForgetPassword',[AdminController::class,'ForgetPassword'])->name('forgetpassword');
    });

    Route::middleware(['auth:admin','PreventBackHistory'])->group(function(){
        Route::get('/',[AdminController::class,'Dashboard'])->name('home');
        Route::view('/profile','dashboard.admin.profile')->name('ProfilePage');
        Route::post('update-profile',[AdminController::class,'UpdateProfile'])->name('UpdateProfile');
        Route::view('change-password','dashboard.admin.changepassword')->name('ChangePassword');
        Route::post('update-password',[AdminController::class,'UpdatePassword'])->name('UpdatePassword');
        Route::post('/logout',[AdminController::class,'logout'])->name('logout');

        // Categories
        Route::prefix('/categories')->group(function(){
            Route::get('/', [CategoryController::class,'index'])->name('categories');
            Route::post('categories/add', [CategoryController::class,'store'])->name('NewCategory');
            Route::post('/verify_category',[CategoryController::class,'VerfiyCategory']);
            Route::post('/UpdateCategory',[CategoryController::class,'UpdateCategory']);
            Route::post('/changestatus',[CategoryController::class,'ChangeStatus'])->name('ChangeCatStatus');
            Route::post('delete-category',[CategoryController::class,'DeleteCategory'])->name('DeleteCategory');
        });


        // Seller Module
        Route::prefix('/seller')->group(function()
        {
            // Seller Requests & Actions
            Route::get('Request',[AdminSellerController::class,'SellerRequestPage'])->name('sellerrequest');
            Route::get('VerifySeller/{sellerid}',[AdminSellerController::class,'VerifySellerPage'])->name('VerifySeller');
            Route::post('requestapproval',[AdminSellerController::class,'Approval'])->name('SellerApproval');

            // Seller List and CRUD
            Route::get('/',[AdminSellerController::class,'SellerList'])->name('SellerList');

            Route::get('/CreateSellerPage',[AdminSellerController::class,'AddSellerPage'])->name('CreateSellerPage');
            Route::post('Create',[AdminSellerController::class,'CreateNewSeller'])->name('SellerCreate');

            Route::post('ChangeSellerStatus',[AdminSellerController::class,'ChangeSellerStatus'])->name('ChangeSellerStatus');
            Route::post('/featuredsellers',[AdminSellerController::class,'featuredsellers'])->name('featuredsellers');
            Route::post('/DeleteSeller',[AdminSellerController::class,'DeleteSeller'])->name('DeleteSeller');

            Route::get('details/{id}',[AdminSellerController::class,'SellerDet'])->name('SellerDetail');

            Route::get('edit/{id}',[AdminSellerController::class,'EditSellerPage'])->name('EditSellerPage');
            Route::get('order-settings-edit/{id}',[AdminSellerController::class,'EditSellerOrderSettingsPage'])->name('EditSellerOrderSettingsPage');
        
            Route::post('updatesellerdet',[AdminSellerController::class,'UpdateSellerDetail'])->name('UpdateSellerDetail');
            Route::post('updatesellerloc',[AdminSellerController::class,'UpdateSellerLocation'])->name('UpdateSellerLocation');
            Route::post('update-banner-image',[AdminSellerController::class,'UpdateBannerImage'])->name('UpdateBannerImage');
            Route::post('seller-cat-update',[AdminSellerController::class,'UpdateSellerCategory'])->name('SellerCatUpdate');
            Route::post('seller-add-shipping-det',[AdminSellerController::class,'AddSellerShippingDetails'])->name('AddSellerShippingDetails');
            Route::post('seller-delete-shipping-det',[AdminSellerController::class,'DeleteSellerShippingDetails'])->name('DeleteSellerShippingDetails');

            Route::post('update-seller-order-settings',[AdminSellerController::class,'UpdateOrderSettings'])->name('UpdateOrderSettings');

            Route::get('/export-sellers',[AdminSellerController::class,'exportSellers'])->name('export-sellers');

            // Seller Category Requests
            Route::get('/seller-cat-request',[AdminSellerController::class,'SellerCategoryRequestPage'])->name('SellerCategoryRequestPage');
            Route::post('/approval-seller-request',[AdminSellerController::class,'ApproveSellerCatRequest'])->name('ApproveSellerCatRequest');

        });


        // Products Module
        Route::prefix('products')->group(function()
        {
            Route::get('/',[AdminProductController::class,'ProductList'])->name('ProductList');
            Route::get('/AddProduct',[AdminProductController::class,'AddProductPage'])->name('AddProduct');
            Route::post('/CreateProduct',[AdminProductController::class,'CreateProduct'])->name('CreateProduct');
            Route::post('/featuredproducts',[AdminProductController::class,'featuredproducts'])->name('featuredproducts');
            Route::post('/productstatus',[AdminProductController::class,'productstatus'])->name('productstatus');

       
            Route::get('/Attributes',[AdminProductController::class,'ProductAttributesList'])->name('ProductAttributes');
            Route::post('/AttributesAdd',[AdminProductController::class,'createattributes'])->name('AddProductAttributes');

            Route::post('seller-categories',[AdminProductController::class,'AdminSellerCategory'])->name('AdminSellerCateory');


            Route::get('product-stocks/{id}',[AdminProductController::class,'ProductStocksPage'])->name('ProductStocksPage');
            // Route::post('post-price_type',[AdminProductController::class,'UpdatePriceType'])->name('UpdatePriceType');
            // Old Flow
            Route::post('add-custom-price',[AdminProductController::class,'AddProductStock'])->name('AddProductStock');
            
            Route::post('update-stock-info',[AdminProductController::class,'UpdateStockInfo'])->name('UpdateStockInfo');
            Route::post('delete-custom-product-stock',[AdminProductController::class,'DeleteCustomProductStock'])->name('DeleteCustomStock');

            // Route::get('/CutomPrice/{id}',[AdminProductController::class,'CutomPrice'])->name('ProductStocksPage');


            Route::get('product-option/{id}',[AdminProductController::class,'ProductOptionsPage'])->name('ProductOptionsPage');
            Route::post('product-add-options',[AdminProductController::class,'AddProductOptions'])->name('AddProductOptions');
            Route::post('update-product-option',[AdminProductController::class,'UpdateProductOption'])->name('UpdateProductOption');
            Route::post('delete-product-option',[AdminProductController::class,'DeleteProductOption'])->name('DeleteProductOption');


            Route::get('product-specification/{id}',[AdminProductController::class,'ProductSpecificationPage'])->name('ProductSpecificationPage');
            Route::post('product-spec',[AdminProductController::class,'AddProductSpec'])->name('AddProductSpec');
            Route::post('product-specification-update',[AdminProductController::class,'UpdateProductSpec'])->name('UpdateProductSpec');
            Route::post('product-specification-delete',[AdminProductController::class,'DeleteProductSpec'])->name('DeleteProductSpec');


            // V2 
            Route::get('product-custom-stock/{id}',[AdminProductController::class,'ProductCutomStock'])->name('ProductCutomStock');
            Route::post('/post-add-option',[AdminProductController::class,'InsertOptionsNew'])->name('addoptionsv2');
            Route::post('update-standard-price-data',[AdminProductController::class,'UpdateStandardPrice'])->name('UpdateStandardPrice');
            

            Route::get("/edit/{id}",[AdminProductController::class,'EditProduct'])->name('EditProductPage');
            Route::post('/UpdateProduct',[AdminProductController::class,'UpdateProduct'])->name('UpdateProduct');
            Route::post('update-additional-image',[AdminProductController::class,'UpdateAdditionalImage'])->name('UpdateProductImage');
            Route::post('delete-additional-image',[AdminProductController::class,'DeleteProductImage'])->name('DeleteProductImage');

            Route::get("/ViewProductDet/{id}",[AdminProductController::class,'ProductDetails'])->name("ProductDetails");

        });


        // Orders Module
        Route::prefix('order')->group(function()
        {
            Route::get('Orderlist',[AdminOrderController::class,'OrderList'])->name('OrderList');
            Route::post('export-order-list',[AdminOrderController::class,'ExportOrders'])->name('ExportOrders');

            Route::get('order-detail/{id}',[AdminOrderController::class,'OrderDetail'])->name('OrderDetail');
            Route::get('OrderIncoice/{id}',[AdminOrderController::class,'OrderInvoice'])->name('OrderInvoice');

            Route::post('update-payment-status',[AdminOrderController::class,'UpdatePaymentStatus'])->name('updatepaymentstatus');


            // Ajax Call
            Route::get('GetOrderStatus',[AdminOrderController::class,'GetOrderStatus']);
            Route::post('UpdateOrderStatus',[AdminOrderController::class,'UpdateOrderStatus']);
            Route::get('GetOrderTrack',[AdminOrderController::class,'GetOrderTrack']);

            // Transaction List
            Route::get('transaction',[AdminOrderController::class,'transactionlist'])->name('TransactionList');
            Route::post('download-transacation',[AdminOrderController::class,'ExportTransaction'])->name('ExportTransaction');
        });


        // Customers Module
        Route::prefix('customers')->group(function()
        {
            Route::get('/',[AdminCustomerController::class,'CustomerList'])->name('CustomerList');
            Route::get("/ViewcustomerDet/{id}",[AdminCustomerController::class,'CustomerDetails'])->name("CustomerDetails");
            Route::post('/customerstatus',[AdminCustomerController::class,'ChangeUserStatus'])->name('CustomerStatus');
        });


        // Settings
        Route::prefix('settings')->group(function()
        {
            Route::get('banners',[OtherModulesController::class,'Banners'])->name('banners');
            Route::post('changebannertype',[OtherModulesController::class,'ChangeBannerType']);
            Route::post('addbanners',[OtherModulesController::class,'AddBanners'])->name('postbanners');
            Route::post('updatebanner',[OtherModulesController::class,'UpdateBanner'])->name('updatebanner');
            Route::get('deletebanner/{id}',[OtherModulesController::class,'DeleteBanner'])->name('deletebanner');


            Route::get('faq',[OtherModulesController::class,'GetFAQPage'])->name('faq');
            Route::post('postfaq',[OtherModulesController::class,'PostFAQ'])->name('postfaq');

            Route::get('/contents',[OtherModulesController::class,'GetContentPage'])->name('contents');
            Route::post('/postcontent',[OtherModulesController::class,'PostContent'])->name('postcontent');
            Route::post('/updatecontent',[OtherModulesController::class,'UpdateContent'])->name('updatecontent');
            Route::post('/deleteContent',[OtherModulesController::class,'DeleteContent'])->name('deleteContent');
            Route::get('ViewContentPage/{title}',[OtherModulesController::class,'ViewContent'])->name('ViewContentPage');

            Route::get('/contact-us',[OtherModulesController::class,'ContactUs'])->name('contactus');
            Route::post('/reply-contact-us',[OtherModulesController::class,'ReplyContactUs'])->name('ReplyContactus');
        });


        // Admin User
        Route::prefix('admin-user')->group(function(){
            Route::get('/',[AdminUserController::class,'AdminList'])->name('adminusers');
            // Route::view('/','dashboard.admin.adminuser.multipleadmin')->name('adminusers');
            Route::view('/add-new-admin','dashboard.admin.adminuser.addnewadmin')->name('addnewadmin');
            Route::post('/add-admin-user',[AdminUserController::class,'AddAdminUser'])->name('AddAdminUser');
            Route::get('edit-admin/{id}',[AdminUserController::class,'EditadminPage'])->name('EditadminPage');
            Route::post('/update-admin-user',[AdminUserController::class,'UpdateAdminProfile'])->name('UpdateAdminProfile');

            Route::post('update-admin-status',[AdminUserController::class,'adminstatus'])->name('adminstatus');
        });
        
    });
});

Route::prefix('App')->group(function(){
    Route::get('{title}',[OtherModulesController::class,'ViewContent'])->name('ViewContentPage');
    Route::get('FAQ',[OtherModulesController::class,'FAQ_Api']);
});

Route::prefix('testing')->group(function(){
    // Route::view('/','TestingPage');
    Route::view('OrderInvoice','dashboard.commonly_used.order_invoice');
    Route::get('TestMail',[TestController::class,'TestMail']);
    Route::get('SendOrderMail/{orderid}',[TestController::class,'TestOrderMail']);
    Route::view('TestUpload','TestImageUpload');
    Route::post('TestImageUpload',[TestController::class,'AWSUploadTest'])->name('TestImageUpload');
    Route::get('/SamplePurchase',[TestController::class,'GetProducts']);
    Route::get('/TestPayment',[TestController::class,'TestPayment']);
    Route::get("payproduct",[TestController::class,'PaymentPost']);
    Route::get('NewAPIResponse',[TestController::class,'NewAPIResponse']);
});



