<?php

use App\Http\Controllers\AddressEditController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductCreateController;
use App\Http\Controllers\ProductDetailController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileEditController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\TopController;
use Illuminate\Support\Facades\Route;


/*Route::get('/register', [RegisterController::class, 'index']);
Route::get('/login', [LoginController::class, 'index']);
Route::get('/item',[ProductDetailController::class, 'index']);
Route::get('/purchase', [PurchaseController::class, 'index']);
Route::get('/purchase/address',[AddressEditController::class, 'index']);
Route::get('/sell', [ProductCreateController::class, 'index']);*/

//*** 認証済み + プロフィール完了必須のルート ***/
Route::middleware(['auth', 'profile.completed'])->group(function () {

    Route::get('/', [TopController::class, 'index']);

    Route::get('/items/{item}', [TopController::class, 'show']);

    Route::get('/mypage',[ProfileController::class,'index']);

});

//*** 認証のみ必要（プロフィール編集は除外）***/
Route::middleware(['auth'])->group(function () {

    // プロフィール編集（初回ログイン時の到達点）
    Route::get('/mypage/profile', [ProfileController::class, 'index']);
    //Route::post('/mypage/profile', [ProfileController::class, 'update']);
});

/*
|--------------------------------------------------------------------------
| ゲスト（未ログイン）
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {

    Route::get('/register', [RegisterController::class, 'index']);
    Route::get('/login', [LoginController::class, 'index']);
});