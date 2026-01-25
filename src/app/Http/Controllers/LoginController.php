<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    /**
     * ログインページ表示
     */
    public function index()
    {
        return view('login');
    }
}
