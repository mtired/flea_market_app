<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductCreateController extends Controller
{
    public function index()
    {
        return view('product_create');
    }
}
