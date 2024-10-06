 <?php

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\FAQController;
// use App\Http\Controllers\TaxController;
// use App\Http\Controllers\CartController;
// use App\Http\Controllers\ShopController;
// use App\Http\Controllers\UserController;
// use App\Http\Controllers\ImageController;
// use App\Http\Controllers\LearnController;
// use App\Http\Controllers\RanksController;
// use App\Http\Controllers\RolesController;
// use App\Http\Controllers\ShipsController;
// use App\Http\Controllers\AuthenController;
// use App\Http\Controllers\BannerController;
// use App\Http\Controllers\BrandsController;
// use App\Http\Controllers\ColorsController;
// use App\Http\Controllers\OrdersController;
// use App\Http\Controllers\AddressController;
// use App\Http\Controllers\MessageController;
// use App\Http\Controllers\ProductController;
// use App\Http\Controllers\RefundsController;
// use App\Http\Controllers\VoucherController;
// use App\Services\DistanceCalculatorService;
// use App\Http\Controllers\CommentsController;
// use App\Http\Controllers\PaymentsController;
// use App\Http\Controllers\ProgrameController;
// use App\Http\Controllers\PurchaseController;
// use App\Http\Controllers\WishlistController;
// use App\Http\Controllers\CategoriesController;
// use App\Http\Controllers\OrderDetailController;
// use App\Http\Controllers\PermissionsController;
// use App\Http\Controllers\PremissionsController;
// use App\Http\Controllers\FollowToShopController;
// use App\Http\Controllers\NotificationController;
// use App\Http\Controllers\Support_mainController;
// use App\Http\Controllers\ProducttocartController;
// use App\Http\Controllers\ProducttoshopController;
// use App\Http\Controllers\ProgramtoshopController;
// use App\Http\Controllers\VoucherToMainController;
// use App\Http\Controllers\VoucherToShopController;
// use App\Http\Controllers\Categori_ShopsController;
// use App\Http\Controllers\CategorilearnsController;
// use App\Http\Controllers\Learning_sellerController;
// use App\Http\Controllers\Notification_to_mainController;
// use App\Http\Controllers\Notification_to_shopController;
// use App\Http\Controllers\CategoriessupportmainController;
// Route::get('/test', function () {
//     return "start";
// })->middleware('CheckPremission:view_orders');

//     Route::group(['middleware' => ['checkToken', 'CheckStatusUser']], function () {


//                 Route::post('categories', [CategoriesController::class, 'store']);
//                 Route::get('categories/{id}', [CategoriesController::class, 'show'])->middleware('CheckPremission:delete_products');
//                 Route::put('categories/{id}', [CategoriesController::class, 'update'])->middleware('CheckPremission:delete_products');
//                 Route::delete('categories/{id}', [CategoriesController::class, 'destroy'])->middleware('CheckPremission:delete_products');
//                 Route::resource('categori_shops', Categori_ShopsController::class);
//                 Route::resource('roles', RolesController::class);
//                 Route::resource('address', AddressController::class);
//                 Route::resource('permission', PremissionsController::class);
//                 Route::post('permission/grant_access', [PremissionsController::class, "grant_access"]);
//                 Route::post('permission/delete_access', [PremissionsController::class, "delete_access"]);
//                 Route::resource('banners', BannerController::class);
//                 Route::resource('faqs', FAQController::class);
//                 Route::resource('taxs', TaxController::class);
//                 Route::resource('ranks', RanksController::class);
//                 Route::resource('ships',ShipsController::class);
//                 Route::resource('payments',PaymentsController::class);
//                 Route::resource('brands',BrandsController::class);
//                 Route::resource('colors',ColorsController::class);
//                 Route::resource('categori_learns', CategorilearnsController::class);
//                 Route::resource('categoriessupportmains', CategoriessupportmainController::class);
//                 Route::resource('learns', LearnController::class);
//                 Route::resource('messages', MessageController::class);
//                 Route::post('messages/detail', [MessageController::class, "store_message_detail"]);
//                 Route::get('messages/detail/{id}', [MessageController::class, "show_message_detail"]);
//                 Route::get('messages/all/detail/{id}', [MessageController::class, "index_message_detail"]);
//                 Route::resource('voucher_main', VoucherToMainController::class);
//                 Route::resource('notification_to_main', Notification_to_mainController::class);
//                 Route::resource('notifications', NotificationController::class);
//                 Route::resource('programes', ProgrameController::class);
//                 Route::resource('notification_to_shops', Notification_to_shopController::class);
//                 Route::resource('vouchers', VoucherController::class);
//                 Route::resource('follows', FollowToShopController::class);
//                 Route::resource('support_main', Support_mainController::class);
//                 Route::resource('Comments', CommentsController::class);
//                 Route::resource('Wishlists', WishlistController::class);
//                 Route::resource('Product_to_carts', ProducttocartController::class);
//                 Route::resource('voucher_shop', VoucherToShopController::class);
//                 Route::get('learning_seller/{shop_id}', [Learning_sellerController::class, 'index']);
//                 Route::get('learning_seller/{shop_id}/{learn_id}', [Learning_sellerController::class, 'show']);
//                 Route::post('learning_seller', [Learning_sellerController::class, 'store']);
//                 Route::put('learning_seller/{id}', [Learning_sellerController::class, 'update']);
//                 Route::delete('learning_seller/delete/{id}', [Learning_sellerController::class, 'destroy']);
//                 Route::resource('learning_seller', Learning_sellerController::class);


//                 Route::post('purchase', [PurchaseController::class, "purchase"]);
//                 Route::post('purchase_to_cart', [PurchaseController::class, "purchaseToCart"]);

//                 //SHOP
//                     Route::post('shops', [ShopController::class, 'store']);
//                     Route::put('shops/{id}', [ShopController::class, 'update']);
//                     Route::delete('shops/{id}', [ShopController::class, 'destroy']);
//                     Route::post('shop/category/{id}/{category_main_id}', [ShopController::class, "category_shop_store"]);
//                     Route::post('shop/manager', [ShopController::class, "shop_manager_store"]);
//                     Route::get('shop/manager/members/{id}', [ShopController::class, "show_shop_members"]);
//                     Route::put('shop/manager/update/members/{id}', [ShopController::class, "update_shop_members"]);
//                     Route::delete('shop/manager/destroy/members/{id}', [ShopController::class, "destroy_members"]);
//                     Route::post('shop/increase_follower/{id}', [ShopController::class, "increase_follower"]);
//                     Route::post('shop/decrease_follower/{id}', [ShopController::class, "decrease_follower"]);
//                     Route::post('shop/store_banner_to_shop/{id}', [ShopController::class, "store_banner_to_shop"]);
//                     Route::post('shop/programe_to_shop/{id}', [ShopController::class, "programe_to_shop"]);
//                     Route::put('shop/update_category_shop/{id}', [ShopController::class, "update_category_shop"]);
//                     Route::get('shop/done_learning_seller/{shop_id}', [ShopController::class, "done_learning_seller"]);
//                     Route::post('shop/voucher/{shop_id}', [ShopController::class, "VoucherToShop"]);
//                     Route::get('shop/order/{id}/{status}', [ShopController::class, "get_order_to_shop_by_status"]);
//                     Route::put('shop/order/{id}', [ShopController::class, "update_status_order"]);
//                 //SHOP
//                 Route::resource('carts', CartController::class);
//                 Route::resource('users', AuthenController::class);
//                 Route::get('user/me', [AuthenController::class, "me"]);
//                 Route::post('user/change_password', [AuthenController::class, "change_password"]);
//                 Route::patch('user/update_profile', [AuthenController::class, "update_profile"]);

//                 Route::resource('orders', OrdersController::class);
//                 Route::get('orders/shop/{id}', [OrdersController::class, "indexOrderToShop"]);
//                 Route::get('order/user', [OrdersController::class, "indexOrderToUser"]);


//             Route::post('user_send/{shop_id}', [MessageController::class, "user_send"]);
//             Route::get('shop_get_message/{shop_id}', [MessageController::class, "shop_get_message"]);
//             Route::get('user_get_message', [MessageController::class, "user_get_message"]);
//             Route::post('shop_send/{mes_id}', [MessageController::class, "shop_send"]);


//             Route::post('products', [ProductController::class, 'store']);
//             Route::put('products/{id}', [ProductController::class, 'update']);
//             Route::delete('products/{id}', [ProductController::class, 'destroy'])->middleware('CheckPremission:delete_products');
//             Route::get('product/get_variant_not_image/{id}', [ProductController::class, 'getVariant']);
//             Route::post('product/update_variant/{id}', [ProductController::class, 'updateVariant']);
//             Route::delete('product/remove_variant/{id}', [ProductController::class, 'removeVariant']);
//             Route::post('product/generate_variants', [ProductController::class, 'generateVariants']);
//             Route::put('product/update_stock_one_variant', [ProductController::class, 'updateStockOneVariant']);
//             Route::put('product/update_stock_all_variant', [ProductController::class, 'updateStockAllVariant']);
//             Route::put('product/update_price_one_variant', [ProductController::class, 'updatePriceOneVariant']);
//             Route::put('product/update_price_all_variant', [ProductController::class, 'updatePriceAllVariant']);
//             Route::post('product/update_image_one_variant', [ProductController::class, 'updateImageOneVariant']);
//             Route::post('product/update_image_all_variant', [ProductController::class, 'updateImageAllVariant']);


//             // Platform Fees Routes
//             Route::get('/platformfees', [TaxController::class, 'indexPlatformFees']);
//             Route::post('/platformfees', [TaxController::class, 'storePlatformFee']);
//             Route::get('/platformfees/{id}', [TaxController::class, 'showPlatformFee']);
//             Route::put('/platformfees/{id}', [TaxController::class, 'updatePlatformFee']);
//             Route::delete('/platformfees/{id}', [TaxController::class, 'destroyPlatformFee']);

//             // Order Tax Details Routes
//             Route::get('/ordertaxdetails', [TaxController::class, 'indexOrderTaxDetails']);
//             Route::post('/ordertaxdetails', [TaxController::class, 'storeOrderTaxDetail']);
//             Route::get('/ordertaxdetails/{id}', [TaxController::class, 'showOrderTaxDetail']);
//             Route::put('/ordertaxdetails/{id}', [TaxController::class, 'updateOrderTaxDetail']);
//             Route::delete('/ordertaxdetails/{id}', [TaxController::class, 'destroyOrderTaxDetail']);

//             // Order Fee Details Routes
//             Route::get('/orderfeedetails', [TaxController::class, 'indexOrderFeeDetails']);
//             Route::post('/orderfeedetails', [TaxController::class, 'storeOrderFeeDetail']);
//             Route::get('/orderfeedetails/{id}', [TaxController::class, 'showOrderFeeDetail']);
//             Route::put('/orderfeedetails/{id}', [TaxController::class, 'updateOrderFeeDetail']);
//             Route::delete('/orderfeedetails/{id}', [TaxController::class, 'destroyOrderFeeDetail']);

//             // Ship Company Routes
//             Route::get('ship_companies', [ShipsController::class, 'ship_companies_index']);
//             Route::post('ship_companies', [ShipsController::class, 'ship_companies_store']);
//             Route::get('ship_companies/{id}', [ShipsController::class, 'ship_companies_show']);
//             Route::put('ship_companies/{id}', [ShipsController::class, 'ship_companies_update']);
//             Route::delete('ship_companies/{id}', [ShipsController::class, 'ship_companies_destroy']);

//             // Ship Service Routes
//             Route::get('/ship_service', [ShipsController::class, 'ship_service_get_one']);
//             Route::get('/ship_service_all', [ShipsController::class, 'ship_service_get_all']);
//             Route::post('/ship_service', [ShipsController::class, 'add_ship_service']);
//             Route::put('/ship_service', [ShipsController::class, 'ship_service_update']);
//             Route::delete('/ship_service', [ShipsController::class, 'ship_service_delete']);

//             // Insurance Routes
//             Route::get('/insurance/{id}', [ShipsController::class, 'insurance_get_one']);
//             Route::get('/insurance_all/{id}', [ShipsController::class, 'insurance_get_all']);
//             Route::post('/insurance', [ShipsController::class, 'insurance_store']);
//             Route::put('/insurance/{id}', [ShipsController::class, 'insurance_update']);
//             Route::delete('/insurance/{id}', [ShipsController::class, 'insurance_delete']);

//             // refnd order shop
//             Route::put('shop/refund/order/{id}', [ShopController::class, "refund_order_update"]);
//             Route::get('shop/refund/order/{id}', [ShopController::class, "refund_order_detail"]);
//             Route::get('shop/refund/order', [ShopController::class, "refund_order_list"]);

// });


// Route::post('user/fogot_password', [AuthenController::class, "fogot_password"]);
// Route::get('user/confirm_mail_change_password/{token}/{email}', [AuthenController::class, "confirm_mail_change_password"])->name('confirm_mail_change_password');
// Route::post('users/register', [AuthenController::class, "register"]);
// Route::post('users/login', [AuthenController::class, "login"]);
// Route::get('confirm/{token}', [AuthenController::class, "confirm"])->name('confirm');

// Route::get('/', function () {
//     return response()->json(['message' => 'Đây là API VNSHOP']);
// });

// Route::get('calculateShippingFee', [DistanceCalculatorService::class, "calculateShippingFee"]);







//         // NO Auth
//         Route::get('products/{id}', [ProductController::class, 'show']);
//         Route::get('products', [ProductController::class, 'index']);
//         Route::get('shops', [ShopController::class, 'index']);
//         Route::get('shops/{id}', [ShopController::class, 'show']);
//         Route::get('shop/get_product_to_shop/{id}', [ShopController::class, "get_product_to_shop"]);
//         Route::get('shop/get_category_shop', [ShopController::class, "get_category_shop"]);
//         Route::get('categories', [CategoriesController::class, 'index']);


//         Route::get('shop/revenue_report', [ShopController::class, 'revenueReport']);
//         Route::get('shop/order_report', [ShopController::class, 'orderReport']);
//         Route::get('shop/best_selling_products', [ShopController::class, 'bestSellingProducts']);
//         Route::post('shop/refund/order/{id}', [ShopController::class, "create_refund_order"]);
//         Route::get('search', [ProductController::class, 'search']); -->


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FAQController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\LearnController;
use App\Http\Controllers\RanksController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\ShipsController;
use App\Http\Controllers\AuthenController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\BrandsController;
use App\Http\Controllers\ColorsController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RefundsController;
use App\Http\Controllers\VoucherController;
use App\Services\DistanceCalculatorService;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\ProgrameController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\OrderDetailController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\PremissionsController;
use App\Http\Controllers\FollowToShopController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Support_mainController;
use App\Http\Controllers\ProducttocartController;
use App\Http\Controllers\ProducttoshopController;
use App\Http\Controllers\ProgramtoshopController;
use App\Http\Controllers\VoucherToMainController;
use App\Http\Controllers\VoucherToShopController;
use App\Http\Controllers\Categori_ShopsController;
use App\Http\Controllers\CategorilearnsController;
use App\Http\Controllers\Learning_sellerController;
use App\Http\Controllers\Notification_to_mainController;
use App\Http\Controllers\Notification_to_shopController;
use App\Http\Controllers\CategoriessupportmainController;
use App\Http\Controllers\SearchController;
Route::get('/search', function () {
    return "start";
})->middleware('CheckPremission:create_category');
    Route::post('/test/search', [SearchController::class, "search"]);
    Route::post('/test/searchshop', [SearchController::class, "searchShop"]);

    Route::group(['middleware' => ['checkToken', 'CheckStatusUser']], function () {


                Route::post('categories', [CategoriesController::class, 'store'])->middleware('CheckPremission:create_category');
                Route::get('categories/{id}', [CategoriesController::class, 'show']);
                Route::put('categories/{id}', [CategoriesController::class, 'update'])->middleware('CheckPremission:update_category');
                Route::delete('categories/{id}', [CategoriesController::class, 'destroy'])->middleware('CheckPremission:delete_category');

                Route::resource('categori_shops', Categori_ShopsController::class);

                Route::resource('roles', RolesController::class)->middleware('CheckRole');

                Route::resource('address', AddressController::class);
                
                Route::resource('permission', PremissionsController::class)->middleware('CheckRole');
                Route::post('permission/grant_access', [PremissionsController::class, "grant_access"])->middleware('CheckRole:OWNER');
                Route::post('permission/delete_access', [PremissionsController::class, "delete_access"])->middleware('CheckRole:OWNER');

                Route::get('banners/client', [BannerController::class, "index"]);
                Route::resource('banners', BannerController::class)->middleware('CheckPremission:handle_banner');

                Route::resource('faqs', FAQController::class)->middleware('CheckRole');

                Route::resource('taxs', TaxController::class)->middleware('CheckRole');

                Route::resource('ranks', RanksController::class)->middleware('CheckRole:Admin');

                Route::resource('ships',ShipsController::class);

                Route::resource('payments',PaymentsController::class)->middleware('CheckRole:Admin');

                Route::resource('ships',ShipsController::class);//chu ro lam nen chua bat premission

                Route::get('brands/client', [BrandsController::class, "index"]);
                Route::resource('brands',BrandsController::class)->middleware('CheckRole:Admin');

                Route::get('colors/client', [ColorsController::class, "index"]);
                Route::resource('colors',ColorsController::class)->middleware('CheckRole:Admin');

                Route::get('categori_learns/client', [CategorilearnsController::class, "index"]);
                Route::resource('categori_learns', CategorilearnsController::class)->middleware('CheckRole:Admin');

                Route::get('categoriessupportmains/client', [CategoriessupportmainController::class, "index"]);
                Route::resource('categoriessupportmains', CategoriessupportmainController::class)->middleware('CheckRole:Admin');

                Route::get('learns/client', [LearnController::class, "index"]);
                Route::resource('learns', LearnController::class)->middleware('CheckRole:Admin');

                Route::resource('messages', MessageController::class);
                Route::post('messages/detail', [MessageController::class, "store_message_detail"]);
                Route::get('messages/detail/{id}', [MessageController::class, "show_message_detail"]);
                Route::get('messages/all/detail/{id}', [MessageController::class, "index_message_detail"]);

                Route::get('voucher_main/client', [VoucherToMainController::class, "index"]);
                Route::resource('voucher_main', VoucherToMainController::class)->middleware('CheckRole:Admin');

                Route::resource('notification_to_main', Notification_to_mainController::class);
                Route::resource('notifications', NotificationController::class);

                Route::resource('programes', ProgrameController::class)->middleware('CheckRole:Admin');

                Route::resource('notification_to_shops', Notification_to_shopController::class);

                Route::get('vouchers/client', [VoucherController::class, "index"]);
                Route::resource('vouchers', VoucherController::class)->middleware('CheckRole:Seller');

                Route::resource('follows', FollowToShopController::class);
                Route::resource('support_main', Support_mainController::class);
                Route::resource('Comments', CommentsController::class);
                Route::resource('Wishlists', WishlistController::class);
                Route::resource('Product_to_carts', ProducttocartController::class);
                Route::resource('voucher_shop', VoucherToShopController::class);

                Route::get('learning_seller/{shop_id}', [Learning_sellerController::class, 'index'])->middleware('CheckRole:Seller');
                Route::get('learning_seller/{shop_id}/{learn_id}', [Learning_sellerController::class, 'show'])->middleware('CheckRole:Seller');
                Route::post('learning_seller', [Learning_sellerController::class, 'store'])->middleware('CheckRole:Admin');
                Route::put('learning_seller/{id}', [Learning_sellerController::class, 'update'])->middleware('CheckRole:Admin');
                Route::delete('learning_seller/delete/{id}', [Learning_sellerController::class, 'destroy'])->middleware('CheckRole:Admin');
                Route::resource('learning_seller', Learning_sellerController::class); // chưa biết phân kiểu gì


                Route::post('purchase', [PurchaseController::class, "purchase"]);
                Route::post('purchase_to_cart', [PurchaseController::class, "purchaseToCart"]);

                //SHOP
                    Route::post('shops', [ShopController::class, 'store']);
                    Route::put('shops/{id}', [ShopController::class, 'update'])->middleware('CheckRole:Admin');
                    Route::delete('shops/{id}', [ShopController::class, 'destroy'])->middleware('CheckRole:Admin');
                    Route::post('shop/category/{id}/{category_main_id}', [ShopController::class, "category_shop_store"])->middleware('CheckRole:Admin');
                    Route::post('shop/manager', [ShopController::class, "shop_manager_store"])->middleware('CheckRole:Seller');
                    Route::get('shop/manager/members/{id}', [ShopController::class, "show_shop_members"])->middleware('CheckRole:Seller');
                    Route::put('shop/manager/update/members/{id}', [ShopController::class, "update_shop_members"])->middleware('CheckRole:Seller');
                    Route::delete('shop/manager/destroy/members/{id}', [ShopController::class, "destroy_members"])->middleware('CheckRole:Seller');


                    Route::post('shop/increase_follower/{id}', [ShopController::class, "increase_follower"]);
                    Route::post('shop/decrease_follower/{id}', [ShopController::class, "decrease_follower"]);
                    Route::post('shop/store_banner_to_shop/{id}', [ShopController::class, "store_banner_to_shop"]);
                    Route::post('shop/programe_to_shop/{id}', [ShopController::class, "programe_to_shop"])->middleware('CheckRole:Seller');
                    Route::put('shop/update_category_shop/{id}', [ShopController::class, "update_category_shop"])->middleware('CheckRole:Seller');
                    Route::get('shop/done_learning_seller/{shop_id}', [ShopController::class, "done_learning_seller"])->middleware('CheckRole:Seller');
                    Route::post('shop/voucher/{shop_id}', [ShopController::class, "VoucherToShop"]);
                    Route::get('shop/order/{id}/{status}', [ShopController::class, "get_order_to_shop_by_status"]);
                    Route::put('shop/order/{id}', [ShopController::class, "update_status_order"]);
                    Route::post('shop/register_ship_giao_hang_nhanh', [ShopController::class, "register_ship_giao_hang_nhanh"]);
                    // Route::post('shop/get_store_ship_giao_hang_nhanh', [ShopController::class, "get_store_ship_giao_hang_nhanh"]);

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

            Route::get('product/approve/{id}', [ProductController::class, 'approve_product']);
            Route::post('products', [ProductController::class, 'store']);
            Route::put('products/{id}', [ProductController::class, 'update']);
            Route::delete('products/{id}', [ProductController::class, 'destroy'])->middleware('CheckPremission:delete_products');
            Route::get('product/get_variant_not_image/{id}', [ProductController::class, 'getVariant']);

            Route::post('products/update_variant/{id}', [ProductController::class, 'updateVariant']);     // update variant

            Route::post('products/update_product/{id}', [ProductController::class, 'updateProduct']);     // update product
            Route::post('products/update/handle/{id}', [ProductController::class, 'handleUpdateProduct']); 

            Route::delete('product/remove_variant/{id}', [ProductController::class, 'removeVariant']);
            Route::post('product/generate_variants', [ProductController::class, 'generateVariants']);
            Route::put('product/update_stock_one_variant', [ProductController::class, 'updateStockOneVariant']);  // câp nhật số lượng kho biến thể
            Route::put('product/update_stock_all_variant', [ProductController::class, 'updateStockAllVariant']);
            Route::put('product/update_price_one_variant', [ProductController::class, 'updatePriceOneVariant']);  // cập nhật giá buieets thể
            Route::put('product/update_price_all_variant', [ProductController::class, 'updatePriceAllVariant']);
            Route::post('product/update_image_one_variant', [ProductController::class, 'updateImageOneVariant']);  // cập nhật ảnh biến thể
            Route::post('product/update_image_all_variant', [ProductController::class, 'updateImageAllVariant']);


            // Platform Fees Routes
            Route::get('/platformfees', [TaxController::class, 'indexPlatformFees']);
            Route::post('/platformfees', [TaxController::class, 'storePlatformFee']);
            Route::get('/platformfees/{id}', [TaxController::class, 'showPlatformFee']);
            Route::put('/platformfees/{id}', [TaxController::class, 'updatePlatformFee']);
            Route::delete('/platformfees/{id}', [TaxController::class, 'destroyPlatformFee']);

            // Order Tax Details Routes
            Route::get('/ordertaxdetails', [TaxController::class, 'indexOrderTaxDetails']);
            Route::post('/ordertaxdetails', [TaxController::class, 'storeOrderTaxDetail']);
            Route::get('/ordertaxdetails/{id}', [TaxController::class, 'showOrderTaxDetail']);
            Route::put('/ordertaxdetails/{id}', [TaxController::class, 'updateOrderTaxDetail']);
            Route::delete('/ordertaxdetails/{id}', [TaxController::class, 'destroyOrderTaxDetail']);

            // Order Fee Details Routes
            Route::get('/orderfeedetails', [TaxController::class, 'indexOrderFeeDetails']);
            Route::post('/orderfeedetails', [TaxController::class, 'storeOrderFeeDetail']);
            Route::get('/orderfeedetails/{id}', [TaxController::class, 'showOrderFeeDetail']);
            Route::put('/orderfeedetails/{id}', [TaxController::class, 'updateOrderFeeDetail']);
            Route::delete('/orderfeedetails/{id}', [TaxController::class, 'destroyOrderFeeDetail']);

            // Ship Company Routes
            Route::get('ship_companies', [ShipsController::class, 'ship_companies_index']);
            Route::post('ship_companies', [ShipsController::class, 'ship_companies_store']);
            Route::get('ship_companies/{id}', [ShipsController::class, 'ship_companies_show']);
            Route::put('ship_companies/{id}', [ShipsController::class, 'ship_companies_update']);
            Route::delete('ship_companies/{id}', [ShipsController::class, 'ship_companies_destroy']);

            // Ship Service Routes
            Route::get('/ship_service', [ShipsController::class, 'ship_service_get_one']);
            Route::get('/ship_service_all/{ship_companies_id}', [ShipsController::class, 'ship_service_get_all']);
            Route::post('/ship_service', [ShipsController::class, 'add_ship_service']);
            Route::put('/ship_service', [ShipsController::class, 'ship_service_update']);
            Route::delete('/ship_service', [ShipsController::class, 'ship_service_delete']);

            // Insurance Routes
            Route::get('/insurance/{id}', [ShipsController::class, 'insurance_get_one']);
            Route::get('/insurance_all/{id}', [ShipsController::class, 'insurance_get_all']);
            Route::post('/insurance', [ShipsController::class, 'insurance_store']);
            Route::put('/insurance/{id}', [ShipsController::class, 'insurance_update']);
            Route::delete('/insurance/{id}', [ShipsController::class, 'insurance_delete']);

            // refnd order shop
            Route::put('shop/refund/order/{id}', [ShopController::class, "refund_order_update"]);
            Route::get('shop/refund/order/{id}', [ShopController::class, "refund_order_detail"]);
            Route::get('shop/refund/order', [ShopController::class, "refund_order_list"]);
            Route::post('shop/refund/order/{id}', [ShopController::class, "create_refund_order"]);

            Route::get('shop/revenue_report', [ShopController::class, 'revenueReport']);
            Route::get('shop/order_report', [ShopController::class, 'orderReport']);
            Route::get('shop/best_selling_products', [ShopController::class, 'bestSellingProducts']);
            Route::get('shops/leadtime/{shop_id}/{order_id}', [ShopController::class, 'leadtime']);

});


Route::post('user/fogot_password', [AuthenController::class, "fogot_password"]);
Route::get('user/confirm_mail_change_password/{token}/{email}', [AuthenController::class, "confirm_mail_change_password"])->name('confirm_mail_change_password');
Route::post('user/restore_account', [AuthenController::class, 'restore_account']);
Route::get('confirm_restore_account/{token}/{email}', [AuthenController::class, "confirm_restore_account"])->name('confirm_restore_account');
Route::post('users/register', [AuthenController::class, "register"]);
Route::post('users/login', [AuthenController::class, "login"]);
Route::get('confirm/{token}', [AuthenController::class, "confirm"])->name('confirm');

Route::get('/', function () {
    return response()->json(['message' => 'Đây là API VNSHOP']);
});

Route::get('calculateShippingFee', [DistanceCalculatorService::class, "calculateShippingFee"]);


        // NO Auth
        Route::get('products/{id}', [ProductController::class, 'show']);
        Route::get('products', [ProductController::class, 'index']);
        Route::get('shops', [ShopController::class, 'index']);
        Route::get('shops/{id}', [ShopController::class, 'show']);
        Route::get('shop/get_product_to_shop/{id}', [ShopController::class, "get_product_to_shop"]);
        Route::get('shop/get_category_shop', [ShopController::class, "get_category_shop"]);
        Route::get('categories', [CategoriesController::class, 'index']);

        Route::get('search', [ProductController::class, 'search']);



        Route::get('get_infomaiton_province_and_city', [PurchaseController::class, 'get_infomaiton_province_and_city']);
        Route::get('get_infomaiton_district', [PurchaseController::class, 'get_infomaiton_district']);
        Route::get('get_infomaiton_ward', [PurchaseController::class, 'get_infomaiton_ward']);

        // API TEST

        Route::get('get_address_user', [PurchaseController::class, 'get_address_user']);

        Route::post('calculateOrderFees_giao_hang_nhanh', [PurchaseController::class, 'calculateOrderFees_giao_hang_nhanh']);

