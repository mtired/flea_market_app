<?php

use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RegisterController::class, 'index']);
Route::post('/register', [RegisterController::class, 'store']);