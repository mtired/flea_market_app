<?php

namespace App\Http\Controllers;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Request;

class TopController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'recommend');

        if ($tab === 'mylist')
        {
            // 仮対応 user_id=1
            $user = User::find(1);

            // 未ログイン対策
            /*if (!auth()->check()) {
                return redirect('/login');
            }*/

           $products = $user
                ? $user->likedItems()->latest('items.created_at')->get()
                : collect();
        }
        else
        {
            $products = Item::latest()->get();
        }

        return view('top', [
            'products' => $products,
            'activeTab' => $tab,
        ]);
    }
}
