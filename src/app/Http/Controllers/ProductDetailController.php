<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Comment;
use App\Models\Like;
use App\Http\Requests\CommentRequest;
use Illuminate\Support\Facades\Auth;

class ProductDetailController extends Controller
{
    /**
     * 商品詳細ページ表示
     */
    public function show(Item $item)
    {
        $isLiked = Auth::check()
            ? $item->likes->contains('user_id', Auth::id())
            : false;
        return view('product_detail', compact('item', 'isLiked'));
    }

    /**
     * コメント送信
     */
    public function storeComment(CommentRequest $request, Item $item)
    {
        if (Auth::guest()) {
            return back()->withErrors(['content' => 'コメントを送信するにはログインが必要です。']);
        }

        Comment::create([
            'item_id' => $item->id,
            'user_id' => Auth::id(),
            'content' => $request->input('content'),
        ]);

        return redirect("/items/{$item->id}");
    }

    /**
     * いいね切り替え
     */
    public function toggleLike(Item $item)
    {
        $userId = Auth::id();

        $liked = Like::where('item_id', $item->id)
            ->where('user_id', $userId)
            ->exists();

        if ($liked) {
            Like::where('item_id', $item->id)
                ->where('user_id', $userId)
                ->delete();
        } else {
            Like::create([
                'item_id' => $item->id,
                'user_id' => $userId,
            ]);
        }

        return back();
    }
}
