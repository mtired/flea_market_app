<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile');
    }

    public function show()
    {
        $user = Auth::user();

        $page = request('page', 'sell');

        // 出品した商品
        $sellItems = $user->items()->latest()->get();

        // 購入した商品（Order → Item）
        $buyItems = $user->orders()
            ->with('item')   // ← ここが重要
            ->latest()
            ->get()
            ->pluck('item')
            ->filter(); // itemがnullのレコードがあれば除外

        // タブ切り替え用（?tab=buy / ?tab=sell )
        $items = $page === 'buy' ? $buyItems : $sellItems;

        return view('profile', compact('user', 'items', 'page'));
    }
}
