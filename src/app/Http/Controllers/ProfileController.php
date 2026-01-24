<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile');
    }

    /**
     * プロフィールページ表示
     */
    public function show()
    {
        $user = Auth::user();

        $profile = Profile::where('user_id', $user->id)->first();

        $page = request('page', 'sell');

        // 出品した商品
        $sellItems = $user->items()->latest()->get();

        // 購入した商品
        $buyItems = $user->orders()
            ->with('item')
            ->latest()
            ->get()
            ->pluck('item')
            ->filter();

        // タブ切り替え用（?tab=buy / ?tab=sell )
        $items = $page === 'buy' ? $buyItems : $sellItems;

        return view('profile', compact('user', 'profile', 'items', 'page'));
    }
}
