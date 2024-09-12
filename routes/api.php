<?php

use App\Http\Controllers\OrderDetailController;
use App\Http\Controllers\Learning_sellerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FAQController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PremissionsController;
use App\Http\Controllers\LearnController;
use App\Http\Controllers\RanksController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\ShipsController;
use App\Http\Controllers\AuthenController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\BrandsController;
use App\Http\Controllers\ColorsController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\CategoriessupportmainController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\ProgrameController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\FollowToShopController;
use App\Http\Controllers\Support_mainController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\ProducttoshopController;
use App\Http\Controllers\ProducttocartController;
use App\Http\Controllers\ProgramtoshopController;
use App\Http\Controllers\VoucherToMainController;
use App\Http\Controllers\CategorilearnsController;
use App\Http\Controllers\Notification_to_mainController;
use App\Http\Controllers\Notification_to_shopController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\Categori_ShopsController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\VoucherToShopController;
use App\Http\Controllers\OrdersController;

    Route::group(['middleware' => ['checkToken', 'CheckStatusUser']], function () {

                Route::resource('categories', CategoriesController::class);
                Route::resource('categori_shops', Categori_ShopsController::class);
                Route::resource('roles', RolesController::class);
                Route::resource('address', AddressController::class);
                Route::resource('permission', PremissionsController::class);
                Route::post('permission/grant_access', [PremissionsController::class, "grant_access"]);
                Route::post('permission/delete_access', [PremissionsController::class, "delete_access"]);
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
                Route::resource('categoriessupportmains', CategoriessupportmainController::class);
                Route::resource('learns', LearnController::class);
                Route::resource('messages', MessageController::class);
                Route::post('messages/detail', [MessageController::class, "store_message_detail"]);
                Route::get('messages/detail/{id}', [MessageController::class, "show_message_detail"]);
                Route::get('messages/all/detail/{id}', [MessageController::class, "index_message_detail"]);
                Route::resource('voucher_main', VoucherToMainController::class);
                Route::resource('notification_to_main', Notification_to_mainController::class);
                Route::resource('notifications', NotificationController::class);
                Route::resource('programes', ProgrameController::class);
                Route::resource('notification_to_shops', Notification_to_shopController::class);
                Route::resource('vouchers', VoucherController::class);
                Route::resource('follows', FollowToShopController::class);
                Route::resource('support_main', Support_mainController::class);
                Route::resource('Comments', CommentsController::class);
                Route::resource('Wishlists', WishlistController::class);
                Route::resource('Product_to_carts', ProducttocartController::class);
                Route::resource('voucher_shop', VoucherToShopController::class);
                Route::get('learning_seller/{shop_id}', [Learning_sellerController::class, 'index']);
                Route::get('learning_seller/{shop_id}/{learn_id}', [Learning_sellerController::class, 'show']);
                Route::post('learning_seller', [Learning_sellerController::class, 'store']);
                Route::put('learning_seller/{id}', [Learning_sellerController::class, 'update']);
                Route::delete('learning_seller/delete/{id}', [Learning_sellerController::class, 'destroy']);
                Route::resource('learning_seller', Learning_sellerController::class);


                Route::post('purchase', [PurchaseController::class, "purchase"]);
                Route::post('purchase_to_cart', [PurchaseController::class, "purchaseToCart"]);

                //SHOP

                    Route::resource('shops', ShopController::class);
                    Route::post('shop/category/{id}/{category_main_id}', [ShopController::class, "category_shop_store"]);
                    Route::post('shop/manager', [ShopController::class, "shop_manager_store"]);
                    Route::get('shop/manager/members/{id}', [ShopController::class, "show_shop_members"]);
                    Route::put('shop/manager/update/members/{id}', [ShopController::class, "update_shop_members"]);
                    Route::delete('shop/manager/destroy/members/{id}', [ShopController::class, "destroy_members"]);
                    Route::post('shop/increase_follower/{id}', [ShopController::class, "increase_follower"]);
                    Route::post('shop/decrease_follower/{id}', [ShopController::class, "decrease_follower"]);
                    Route::post('shop/store_banner_to_shop/{id}', [ShopController::class, "store_banner_to_shop"]);
                    Route::post('shop/programe_to_shop/{id}', [ShopController::class, "programe_to_shop"]);
                    Route::get('shop/get_product_to_shop/{id}', [ShopController::class, "get_product_to_shop"]);
                    Route::get('shop/get_category_shop', [ShopController::class, "get_category_shop"]);
                    Route::put('shop/update_category_shop/{id}', [ShopController::class, "update_category_shop"]);
                    Route::get('shop/done_learning_seller/{id}', [ShopController::class, "done_learning_seller"]);

                //SHOP
                Route::resource('carts', CartController::class);
                Route::resource('users', AuthenController::class);
                Route::get('user/me', [AuthenController::class, "me"]);
                Route::post('user/change_password', [AuthenController::class, "change_password"]);
                Route::patch('user/update_profile', [AuthenController::class, "update_profile"]);

                Route::resource('orders', OrdersController::class);
                Route::get('orders/shop/{id}', [OrdersController::class, "indexOrderToShop"]);
                Route::get('order/user', [OrdersController::class, "indexOrderToUser"]);


            Route::post('user_send/{shop_id}', [MessageController::class, "user_send"]);
            Route::get('shop_get_message/{shop_id}', [MessageController::class, "shop_get_message"]);
            Route::get('user_get_message', [MessageController::class, "user_get_message"]);
            Route::post('shop_send/{mes_id}', [MessageController::class, "shop_send"]);



});
Route::get('login', [MessageController::class, "login"]);


Route::post('user/fogot_password', [AuthenController::class, "fogot_password"]);
Route::get('user/confirm_mail_change_password/{token}/{email}', [AuthenController::class, "confirm_mail_change_password"])->name('confirm_mail_change_password');
Route::post('users/register', [AuthenController::class, "register"]);
Route::post('users/login', [AuthenController::class, "login"]);
Route::get('confirm/{token}', [AuthenController::class, "confirm"])->name('confirm');

Route::resource('images', ImageController::class);

