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




/*
|--------------------------------------------------------------------------
| 公開ページ（未ログインでも問題なし）
|--------------------------------------------------------------------------
*/
Route::get('/', [TopController::class, 'index'])->name('top');
Route::get('/items/{item}', [ProductDetailController::class, 'show'])
    ->name('items.show');

/*
|--------------------------------------------------------------------------
| ゲスト（未ログイン）
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {

    Route::get('/register', [RegisterController::class, 'index']);
    Route::get('/login', [LoginController::class, 'index'])->name('login');
});


/*
|--------------------------------------------------------------------------
| ログイン済み
|--------------------------------------------------------------------------
| ・初回登録直後は常にプロフィール編集画面へ飛ぶ
| ・登録後についてもプロフィール変更できるようにしておく
*/
Route::middleware('auth')->group(function () {

    // プロフィール編集
    Route::get('/mypage/profile', [ProfileEditController::class, 'edit'])
        ->name('profile.edit');

    Route::put('/mypage/profile', [ProfileEditController::class, 'update'])
        ->name('profile.update');
});


/*
|--------------------------------------------------------------------------
| ログイン済み + プロフィール完了必須
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'profile.completed'])->group(function () {

    // マイページ
    Route::get('/mypage', [ProfileController::class, 'show'])
        ->name('mypage');

    // コメント・いいね
    Route::post('/items/{item}/comments', [ProductDetailController::class, 'storeComment'])
        ->name('items.comments.store');

    Route::post('/items/{item}/like', [ProductDetailController::class, 'toggleLike'])
        ->name('items.like.toggle');

    // 購入
    Route::get('/purchase/{item}', [PurchaseController::class, 'show'])
        ->name('purchase.show');

    Route::post('/purchase/{item}', [PurchaseController::class, 'store'])
        ->name('purchase.store');

    // 住所変更
    Route::get('/purchase/address/{item}', [AddressEditController::class, 'edit'])
        ->name('purchase.address.edit');

    Route::put('/purchase/address/{item}', [AddressEditController::class, 'update'])
        ->name('purchase.address.update');

    // 出品
    Route::get('/sell', [ProductCreateController::class, 'create'])
        ->name('items.create');

    Route::post('/sell', [ProductCreateController::class, 'store'])
        ->name('items.store');
});

