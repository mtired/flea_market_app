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
        $keyword = $request->query('keyword');

        // 仮ログインユーザー（後で Auth::id() に置き換える）
        $loginUserId = 2;

        if ($tab === 'mylist') {

            $user = User::find($loginUserId);

                        $products = $user
                ? $user->likedItems()
                    ->when($keyword, function ($query) use ($keyword) {
                        // 2. 商品名の部分一致
                        $query->where('items.name', 'like', "%{$keyword}%");
                    })
                    ->latest('items.created_at')
                    ->get()
                : collect();

        }
        else{
            // 自分で出品した商品以外を表示
            $products = Item::query()
            ->where('user_id', '!=', $loginUserId)
            ->when($keyword, function ($query) use ($keyword) {
                // 2. 商品名の部分一致
                $query->where('name', 'like', "%{$keyword}%");
            })
            ->latest()
            ->get();
        }

        return view('top', [
            'products' => $products,
            'activeTab' => $tab,
        ]);
    }
}
