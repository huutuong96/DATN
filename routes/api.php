<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Notification_to_mainController;
use App\Http\Controllers\AuthenController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\RanksController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\BrandsController;
use App\Http\Controllers\ColorsController;
use App\Http\Controllers\CouponsController;
use App\Http\Controllers\FAQController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\ShipsController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\VoucherToMainController;
use App\Http\Controllers\CategorilearnsController;
use App\Http\Controllers\LearnController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ProgrameController;
use App\Http\Controllers\Notification_to_shopController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\FollowToShopController;
use App\Http\Controllers\Notifications;
use App\Http\Controllers\Support_mainController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\Categori_ShopsController;
use App\Http\Controllers\PurchaseController;



Route::group(['middleware' => ['checkToken', 'CheckStatusUser', 'CheckRole', 'CheckPermission']], function () {

            Route::resource('users', AuthenController::class);
            Route::resource('categories', CategoriesController::class);
            Route::resource('categori_shops', Categori_ShopsController::class);
            Route::post('user/me', [AuthenController::class, "me"]);
            Route::resource('role', RolesController::class);
            Route::resource('address', AddressController::class);
            Route::resource('permission', PermissionsController::class);
            Route::post('permission/grant_access', [PermissionsController::class, "grant_access"]);
            Route::post('permission/delete_access', [PermissionsController::class, "delete_access"]);
            Route::resource('banners', BannerController::class);
            Route::resource('products', ProductController::class);
            Route::resource('faqs', FAQController::class);
            Route::resource('taxs', TaxController::class);
            Route::resource('ranks', RanksController::class);
            Route::resource('ships',ShipsController::class);
            Route::resource('payments',PaymentsController::class);
            Route::resource('brands',BrandsController::class);
            Route::resource('colors',ColorsController::class);
            Route::resource('categori_learns', CategorilearnsController::class);
            Route::resource('Categoriessupportmains', CategoriessupportmainController::class);
            Route::resource('learn', LearnController::class);
            Route::resource('messages', MessageController::class);
            Route::post('messages/detail', [MessageController::class, "store_message_detail"]);
            Route::get('messages/detail/{id}', [MessageController::class, "show_message_detail"]);
            Route::get('messages/all/detail/{id}', [MessageController::class, "index_message_detail"]);
            Route::resource('shops', ShopController::class);
            Route::post('shop/manager', [ShopController::class, "shop_manager_store"]);
            Route::get('shop/manager/members/{id}', [ShopController::class, "show_shop_members"]);
            Route::put('shop/manager/update/members/{id}', [ShopController::class, "update_shop_members"]);
            Route::delete('shop/manager/destroy/members/{id}', [ShopController::class, "destroy_members"]);
            Route::resource('voucher_main', VoucherToMainController::class);
            Route::resource('coupons',CouponsController::class);
            Route::get('coupons/shop/{id}', [CouponsController::class, "index_to_shop"]);
            Route::post('coupons/shop/add/{id}', [CouponsController::class, "store_to_shop"]);
            Route::get('coupons/shop/detail/{id}', [CouponsController::class, "show_to_shop"]);
            Route::put('coupons/shop/update/{id}', [CouponsController::class, "update_to_shop"]);
            Route::delete('coupons/shop/delete/{id}', [CouponsController::class, "destroy_to_shop"]);
            Route::resource('notification_to_main', Notification_to_mainController::class);
            Route::resource('notifications', Notifications::class);
            Route::resource('programes', ProgrameController::class);
            Route::resource('notification_to_shops', Notification_to_shopController::class);
            Route::resource('vouchers', VoucherController::class);
            Route::resource('follows', FollowToShopController::class);
            Route::resource('support_main', Support_mainController::class);


            Route::post('purchase', [PurchaseController::class, "purchase"]);

});

Route::post('users/register', [AuthenController::class, "register"]);
Route::post('users/login', [AuthenController::class, "login"]);




