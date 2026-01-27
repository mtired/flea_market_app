<?php

namespace App\Http\Controllers;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TopController extends Controller
{
    /**
     * トップページ表示（検索を含む）
     */
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'recommend');
        $keyword = $request->query('keyword');

        $loginUserId = Auth::id();

        if ($tab === 'mylist') {
            if (Auth::guest()){
                $products = collect();
            }
            else
            {
                $user = User::find($loginUserId);
                $products = $user
                ? $user->likedItems()
                        ->when($keyword, function ($query) use ($keyword) {
                        // 商品名の部分一致
                        $query->where('items.name', 'like', "%{$keyword}%");
                    })
                    ->latest('items.created_at')
                    ->get()
                : collect();
            }
        }
        else{
            $products = Item::query()
            ->when($loginUserId, function ($query) use ($loginUserId) {
                // ログインしているときだけ自分の商品を除外
                $query->where('user_id', '!=', $loginUserId);
            })
            ->when($keyword, function ($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            })
            ->latest()
            ->get();
        }

        return view('top', [
            'products' => $products,
            'activeTab' => $tab,
            'keyword'   => $keyword,
        ]);
    }
}
