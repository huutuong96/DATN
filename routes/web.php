<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VnshopController;
use Illuminate\Http\Request;
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
use App\Http\Controllers\configController;



Route::get('/', [VnshopController::class, 'login'])->name('login');
Route::group(['middleware' => ['checkToken']], function () {
    Route::get('/dashboard', [VnshopController::class, 'dashboard'])->name('dashboard');
    Route::get('/store', [VnshopController::class, 'store'])->name('store');
    Route::get('/list-category', [VnshopController::class, 'list_category'])->name('list_category');
    Route::get('/change-category', [VnshopController::class, 'changeCategory'])->name('change_category');
    Route::get('/product_all', [ProductController::class, 'ProductAll'])->name('product_all');
    Route::get('/product-waiting-approval', [ProductController::class, 'productWaitingApproval'])->name('product-waiting-approval');
    Route::post('/products/{id}/approve', [ProductController::class, 'approveProduct'])->name('products.approve');
    Route::post('/products/{id}/reject', [ProductController::class, 'rejectProduct'])->name('products.reject');
    // Route::post('/products/{id}/report', [ProductController::class, 'reportProduct'])->name('products.report');
});

