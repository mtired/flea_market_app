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

Route::get('/',[TopController::class, 'index']);
Route::get('/items/{item}', [TopController::class, 'show']);
Route::get('/register', [RegisterController::class, 'index']);
Route::get('/login', [LoginController::class, 'index']);
Route::get('/item',[ProductDetailController::class, 'index']);
Route::get('/purchase', [PurchaseController::class, 'index']);
Route::get('/purchase/address',[AddressEditController::class, 'index']);
Route::get('/sell', [ProductCreateController::class, 'index']);
Route::get('/mypage',[ProfileController::class,'index']);
Route::get('/mypage/profile',[ProfileEditController::class, 'index']);