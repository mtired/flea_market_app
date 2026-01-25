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

// 商品一覧画面 (トップ画面)
Route::get('/', [TopController::class, 'index'])->name('top');

// 商品詳細画面
Route::get('/items/{item}', [ProductDetailController::class, 'show'])
    ->name('items.show');

/*
|--------------------------------------------------------------------------
| ゲスト（未ログイン）
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {

    // ユーザ登録画面
    Route::get('/register', [RegisterController::class, 'index']);

    // ログイン画面
    Route::get('/login', [LoginController::class, 'index'])->name('login');
});


/*
|--------------------------------------------------------------------------
| ログイン済み（認証誘導画面はメール未認証で見ることができるようにする）
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // メール認証誘導画面
    Route::get('/email/verify', function () {
        return view('verify_email');
    })->name('verification.notice');
});


/*
|--------------------------------------------------------------------------
| ログイン済み + メール認証済み + プロフィール未完了（プロフ設定画面へ遷移）
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // プロフィール編集
    Route::get('/mypage/profile', [ProfileEditController::class, 'edit'])
        ->name('profile.edit');

    Route::put('/mypage/profile', [ProfileEditController::class, 'update'])
        ->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| ログイン済み + メール認証済み + プロフィール完了
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'profile.completed', 'verified'])->group(function () {

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

