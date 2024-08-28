<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
<<<<<<< HEAD
=======
use App\Http\Controllers\AuthenController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\RanksController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\CategoriessupportmainController;

use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\BrandsController;
use App\Http\Controllers\ColorsController;
use App\Http\Controllers\FAQController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\ShipsController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\CategorilearnsController;
use App\Http\Controllers\LearnController;
>>>>>>> 64449045de4953f33495614cf40cae6b40a0b6ec

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

<<<<<<< HEAD
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
=======
Route::group(['middleware' => ['checkToken', 'CheckStatusUser', 'CheckRole', 'CheckPermission']], function () {

            Route::resource('user', AuthenController::class);
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
            
            // categori learns
            Route::resource('categori_learns', CategorilearnsController::class);
            // address
            Route::resource('address', AddressController::class);
            //Role
            Route::resource('roles', RolesController::class);
            //LEARN
            Route::resource('learns', LearnController::class);

            Route::resource('Categoriessupportmains', CategoriessupportmainController::class);
           


            Route::resource('ships',ShipsController::class);
            Route::resource('payments',PaymentsController::class);
            Route::resource('brands',BrandsController::class);
            Route::resource('colors',ColorsController::class);

 




});

Route::post('user/register', [AuthenController::class, "register"]);
Route::post('user/login', [AuthenController::class, "login"]);

>>>>>>> 64449045de4953f33495614cf40cae6b40a0b6ec
