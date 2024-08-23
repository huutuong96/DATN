<?php

use App\Http\Controllers\Notification_to_mainController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['middleware' => ['checkToken', 'CheckStatusUser', 'CheckRole', 'CheckPermission']], function () {

            Route::resource('users', AuthenController::class);
            Route::post('user/me', [AuthenController::class, "me"]);
            Route::resource('role', RolesController::class);
            Route::resource('address', AddressController::class);
            Route::resource('permission', PermissionsController::class);
            Route::post('permission/grant_access', [PermissionsController::class, "grant_access"]);
            Route::post('permission/delete_access', [PermissionsController::class, "delete_access"]);

            Route::resource('banners', BannerController::class);
            Route::resource('faqs', FAQController::class);
            Route::resource('taxs', TaxController::class);
            Route::resource('ranks', RanksController::class);


            Route::resource('ships',ShipsController::class);
            Route::resource('payments',PaymentsController::class);
            Route::resource('brands',BrandsController::class);
            Route::resource('colors',ColorsController::class);

            Route::resource('categori_learns', CategorilearnsController::class);
            Route::resource('address', AddressController::class);
            Route::resource('role', RolesController::class);
            Route::resource('learn', LearnController::class);

            Route::resource('address', AddressController::class);
            Route::resource('messages', MessageController::class);


            Route::resource('role', RolesController::class);
            Route::resource('shops', ShopController::class);

            Route::resource('voucher_main', VoucherToMainController::class);

            Route::resource('coupons',CouponsController::class);
            Route::resource('notification_to_main', Notification_to_mainController::class);


});





=======
Route::post('users/register', [AuthenController::class, "register"]);
Route::post('users/login', [AuthenController::class, "login"]);




