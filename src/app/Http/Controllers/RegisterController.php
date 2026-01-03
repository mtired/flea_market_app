<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class RegisterController extends Controller
{
    // fortify実装後クラスごと消す
    public function index(Request $request)
    {
        return view('register');
    }

    public function store(RegisterRequest $request, CreateNewUsers $creator)
    {
        $input = $request->safe()->only(['name', 'email', 'password']);
        $user = $creator->create($input);
        //$user = $creator->create($request->validated());
        return redirect('/register');
    }
}
