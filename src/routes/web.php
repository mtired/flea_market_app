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
Route::get('/', [TopController::class, 'index']);
Route::get('/item',[ProductDetailController::class, 'index']);
Route::get('/items/{item}', [TopController::class, 'show']);
Route::get('/purchase', [PurchaseController::class, 'index']);
Route::get('/purchase/address',[AddressEditController::class, 'index']);
Route::get('/sell', [ProductCreateController::class, 'index']);
Route::get('/mypage',[ProfileController::class,'index']);
Route::get('/mypage/profile', [ProfileEditController::class, 'index']);*/

//*** 認証済み + プロフィール完了必須のルート ***/ //[TODO]:プロフィール編集画面作成したら有効化
/*Route::middleware(['auth', 'profile.completed'])->group(function () {

    Route::get('/', [TopController::class, 'index']);

    Route::get('/items/{item}', [TopController::class, 'show']);

    Route::get('/mypage',[ProfileController::class,'index']);

});*/
    Route::get('/', [TopController::class, 'index']);
    Route::get('/', [TopController::class, 'index'])->name('top');
    Route::get('/items/{item}', [ProductDetailController::class, 'show']);
    
    /*** 認証のみ必要（プロフィール編集は除外）***/ 
    Route::middleware(['auth'])->group(function () {

    // プロフィール編集（初回ログイン時の到達点）
    // [TODO]:プロフィール編集画面作成したらこの処理のみ追加

    Route::post('/items/{item}/comments', [ProductDetailController::class, 'storeComment'])
    ->middleware('auth')
    ->name('items.comments.store');
    
    Route::post('/items/{item}/like', [ProductDetailController::class, 'toggleLike'])
    ->middleware('auth')
    ->name('items.like.toggle');

    Route::get('/mypage', [ProfileController::class, 'show'])
    ->middleware('auth')
    ->name('mypage');

    Route::get('/mypage/profile', [ProfileEditController::class, 'edit'])
        ->name('profile.edit');

    Route::put('/mypage/profile', [ProfileEditController::class, 'update'])
        ->name('profile.update');

    
    // 購入画面表示
    Route::get('/purchase/{item}', [PurchaseController::class, 'show'])
        ->name('purchase.show');

    // 購入処理（注文作成など）
    Route::post('/purchase/{item}', [PurchaseController::class, 'store'])
        ->name('purchase.store');

    // 住所変更画面表示
    Route::get('/purchase/address/{item}', [AddressEditController::class, 'edit'])
        ->name('purchase.address.edit');

    // 住所更新処理
    Route::put('/purchase/address/{item}', [AddressEditController::class, 'update'])
        ->name('purchase.address.update');

    // 出品画面表示
    Route::get('/sell', [ProductCreateController::class, 'create'])->name('items.create');

    // 出品処理
    Route::post('/sell', [ProductCreateController::class, 'store'])->name('items.store');
});

/*
|--------------------------------------------------------------------------
| ゲスト（未ログイン）
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {

    Route::get('/register', [RegisterController::class, 'index']);
    Route::get('/login', [LoginController::class, 'index'])->name('login');
});