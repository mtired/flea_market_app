<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RegisterController extends Controller
{
    // fortify実装後クラスごと消す
    public function index(Request $request)
    {
        return view('register');
    }
}
