<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\RanksController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionsController;

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


            Route::resource('user', AuthenController::class);
            Route::post('user/me', [AuthenController::class, "me"]);

            Route::resource('role', RolesController::class);
            Route::resource('rank', RanksController::class);
            Route::resource('address', AddressController::class);
            Route::resource('permission', PermissionsController::class);

            Route::post('permission/grant_access', [PermissionsController::class, "grant_access"]);
            Route::post('permission/delete_access', [PermissionsController::class, "delete_access"]);

});




Route::post('user/register', [AuthenController::class, "register"]);
Route::post('user/login', [AuthenController::class, "login"]);

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::resource('users', UserController::class);
