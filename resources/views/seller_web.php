<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BazaartController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\Seller\SellerController;
use App\Http\Controllers\Seller\SellerCategoryController;
use App\Http\Controllers\Seller\SellerOrderController;
use App\Http\Controllers\Seller\SellerProductController;
use App\Http\Controllers\Seller\SellerSettingsController;
use App\Models\Order;
use App\Models\Seller;

Route::prefix('seller')->name('seller.')->group(function(){

    Route::middleware(['guest:seller','PreventBackHistory'])->group(function(){
        Route::view('/login','dashboard.seller.login')->name('login');
        Route::get('/register',[SellerController::class,'registerpage'])->name('register');
        // Route::view('/register','dashboard.seller.register')->name('register');
        Route::post('/create',[SellerController::class,'create'])->name('create');
        Route::post('/check',[SellerController::class,'check'])->name('check');
        Route::get('/getforgetpassword',[SellerController::class,'getforgetpassword'])->name('getforgetpassword');
        Route::post('/ForgetPassword',[SellerController::class,'ForgetPassword'])->name('forgetpassword');

    });

    Route::middleware(['auth:seller','PreventBackHistory'])->group(function()
    {

        // Route::view('/','dashboard.seller.home')->name('home');
        Route::get('/',[SellerController::class,'Dashboard'])->name('home');
        Route::post('logout',[SellerController::class,'logout'])->name('logout');
        Route::view('change-password','dashboard.seller.changepassword')->name('ChangePassword');
        Route::post('update-password',[SellerController::class,'UpdatePassword'])->name('UpdatePassword');


        Route::get('categories',[SellerCategoryController::class,'CategoryList'])->name('categories');
        Route::post('categories/add', [SellerCategoryController::class,'store'])->name('NewCategory');
        Route::post('/assign-seller-category',[SellerCategoryController::class,'AssignCategory'])->name('AssignCategory');

        Route::get('details',[SellerSettingsController::class,'SellerDet'])->name('SellerDetail');

        Route::post('updatesellerdet',[SellerSettingsController::class,'UpdateSellerDetail'])->name('UpdateSellerDetail');
        Route::post('update-banner-image',[SellerSettingsController::class,'UpdateBannerImage'])->name('UpdateBannerImage');
        Route::post('seller-cat-update',[SellerSettingsController::class,'UpdateSellerCategory'])->name('SellerCatUpdate');

        Route::post('seller-add-shipping-det',[SellerSettingsController::class,'AddSellerShippingDetails'])->name('AddSellerShippingDetails');
        Route::post('seller-delete-shipping-det',[SellerSettingsController::class,'DeleteSellerShippingDetails'])->name('DeleteSellerShippingDetails');

        Route::post('update-seller-order-settings',[SellerSettingsController::class,'UpdateOrderSettings'])->name('UpdateOrderSettings');


        Route::prefix('products')->group(function()
        {
            Route::get('/',[SellerProductController::class,'ProductList'])->name('ProductList');
            Route::get("/ViewProductDet/{id}",[SellerProductController::class,'ProductDetails'])->name("ProductDetails");

            Route::get('AddProduct',[SellerProductController::class,'AddProductPage'])->name('AddProduct');
            Route::post('/CreateProduct',[SellerProductController::class,'CreateProduct'])->name('CreateProduct');

            Route::get("/edit/{id}",[SellerProductController::class,'EditProduct'])->name('EditProductPage');
            Route::post('/UpdateProduct',[SellerProductController::class,'UpdateProduct'])->name('UpdateProduct');


            Route::get('/CutomPrice/{id}',[SellerProductController::class,'CutomPrice'])->name('CutomPrice');
            Route::post('PostCustomPrice',[SellerProductController::class,'PostCustomPrice'])->name('PostCustomPrice');
            Route::post('add-custom-price',[SellerProductController::class,'AddProductStock'])->name('AddProductStock');

            Route::get('/GetAttributes',[SellerProductController::class,'GetAttributes']);

            Route::post('/featuredproducts',[SellerProductController::class,'featuredproducts'])->name('featuredproducts');
            Route::post('/productstatus',[SellerProductController::class,'productstatus'])->name('productstatus');

            // Route::get('/Reviews',[SellerProductController::class,'ProductReviewList'])->name('ProductReviews');

            // Route::get('product-stocks/{id}',[SellerProductController::class,'ProductStocksPage'])->name('ProductStocksPage');

            // // Route::get('/CutomPrice/{id}',[SellerProductController::class,'CutomPrice'])->name('ProductStocksPage');
            // Route::post('PostCustomPrice',[SellerProductController::class,'PostCustomPrice'])->name('PostCustomPrice');

            // Route::get('product-option/{id}',[SellerProductController::class,'ProductOptionsPage'])->name('ProductOptionsPage');
            // Route::post('product-add-options',[SellerProductController::class,'AddProductOptions'])->name('AddProductOptions');
            // Route::post('product-spec',[SellerProductController::class,'AddProductSpec'])->name('AddProductSpec');

            // Stocks
            Route::get('product-stocks/{id}',[SellerProductController::class,'ProductStocksPage'])->name('ProductStocksPage');
            Route::post('post-price_type',[SellerProductController::class,'UpdatePriceType'])->name('UpdatePriceType');
            Route::post('PostCustomPrice',[SellerProductController::class,'PostCustomPrice'])->name('PostCustomPrice');
            Route::post('update-stock-info',[SellerProductController::class,'UpdateStockInfo'])->name('UpdateStockInfo');
            Route::post('delete-custom-product-stock',[SellerProductController::class,'DeleteCustomProductStock'])->name('DeleteCustomStock');

            // Route::get('/CutomPrice/{id}',[SellerProductController::class,'CutomPrice'])->name('ProductStocksPage');

            // Options
            Route::get('product-option/{id}',[SellerProductController::class,'ProductOptionsPage'])->name('ProductOptionsPage');
            Route::post('product-add-options',[SellerProductController::class,'AddProductOptions'])->name('AddProductOptions');
            Route::post('update-product-option',[SellerProductController::class,'UpdateProductOption'])->name('UpdateProductOption');
            Route::post('delete-product-option',[SellerProductController::class,'DeleteProductOption'])->name('DeleteProductOption');

            // Specifications
            Route::get('product-specification/{id}',[SellerProductController::class,'ProductSpecificationPage'])->name('ProductSpecificationPage');
            Route::post('product-spec',[SellerProductController::class,'AddProductSpec'])->name('AddProductSpec');
            Route::post('product-specification-update',[SellerProductController::class,'UpdateProductSpec'])->name('UpdateProductSpec');
            Route::post('product-specification-delete',[SellerProductController::class,'DeleteProductSpec'])->name('DeleteProductSpec');

        });

        Route::prefix('order')->group(function(){
            // Route::get('/',[SellerOrderController::class,'Orders'])->name('Orders');

            Route::get('Orderlist',[SellerOrderController::class,'OrderList'])->name('OrderList');
            Route::post('export-order-list',[SellerOrderController::class,'ExportOrders'])->name('ExportOrders');

            Route::post('update-payment-status',[AdminOrderController::class,'UpdatePaymentStatus'])->name('updatepaymentstatus');

            Route::get('order-detail/{id}',[SellerOrderController::class,'OrderDetail'])->name('OrderDetail');
            Route::get('OrderIncoice/{id}',[SellerOrderController::class,'OrderInvoice'])->name('OrderInvoice');

            // Route::get('OrderList',[SellerOrderController::class,'OrderList'])->name('OrderList');
            Route::get('GetOrderStatus',[SellerOrderController::class,'GetOrderStatus']);
            Route::post('UpdateOrderStatus',[SellerOrderController::class,'UpdateOrderStatus']);
            Route::get('GetOrderTrack',[SellerOrderController::class,'GetOrderTrack']);

            // Transaction List
            Route::get('/transaction',[SellerOrderController::class,'transactionlist'])->name('TransactionList');
            Route::post('download-transacation',[SellerOrderController::class,'ExportTransaction'])->name('ExportTransaction');

        });

        Route::prefix('settings')->group(function(){
            Route::get('/',[SellerSettingsController::class,'SellerSettings'])->name('SellerSettings');
        });
    });
});
