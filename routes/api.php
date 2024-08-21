<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\RanksController;
use App\Http\Controllers\AddressController;

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
Route::group(['middleware' => 'checkToken'], function () {
    Route::resource('user', AuthenController::class);
    Route::post('user/me', [AuthenController::class, "me"]);

    Route::resource('role', RolesController::class);
    Route::resource('rank', RanksController::class);
    Route::resource('address', AddressController::class);
});


Route::post('user/register', [AuthenController::class, "register"]);
Route::post('user/login', [AuthenController::class, "login"]);
