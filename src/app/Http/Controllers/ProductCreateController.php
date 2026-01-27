<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Condition;
use App\Models\Item;
use App\Http\Requests\ProductCreateRequest;
use Illuminate\Support\Facades\Auth;

class ProductCreateController extends Controller
{
    /**
     * 商品出品ページ表示
     */
    public function create()
    {
        $categories = Category::orderBy('id')->get();
        $conditions = Condition::orderBy('id')->get();

        return view('product_create', compact('categories', 'conditions'));
    }

    /**
     * 出品処理
     */
    public function store(ProductCreateRequest $request)
    {
        $validated = $request->validated();

        // 画像保存（publicディスク）
        $imagePath = $request->file('image')->store('item_images', 'public');

        $item = Item::create([
            'user_id'       => Auth::id(),
            'condition_id'  => $validated['condition_id'],
            'name'          => $validated['name'],
            'brand'         => $validated['brand'],
            'description'   => $validated['description'],
            'price'         => $validated['price'],
            'brand'         => $validated['brand'],
            'image'         => $imagePath,
            'status'        => 0
        ]);

        // カテゴリ（複数）を紐付け
        // Request側は category_ids[] の配列になっている想定
        $item->categories()->sync($validated['category_ids']);

        // 商品詳細へ（ルート名はあなたの環境に合わせて）
        return redirect()->route('items.show', $item->id);
    }
}
