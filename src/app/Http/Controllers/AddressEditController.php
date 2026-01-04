<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AddressEditController extends Controller
{
    public function index()
    {
        return view('address_edit');
    }
}
