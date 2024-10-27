<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VnshopController;




Route::get('/', [VnshopController::class, 'login'])->name('login');
Route::group(['middleware' => ['checkToken']], function () {
    Route::get('/dashboard', [VnshopController::class, 'dashboard'])->name('dashboard');
    Route::get('/store', [VnshopController::class, 'store']);
});

