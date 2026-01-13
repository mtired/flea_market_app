<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Profile;
use App\Models\Order;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index()
    {
        return view('purchase');
    }

    /**
     * 購入画面表示
     */
    public function show(Item $item)
    {
        $user = Auth::user();

        // 配送先（例：profiles から取得する想定）
        $address = Profile::where('user_id', $user->id)->first();

        return view('purchase', compact('item', 'address'));
    }

    /**
     * 購入処理
     */
    public function store(Request $request, Item $item)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'payment_method' => ['required', 'in:convenience,card,bank'],
        ], [
            'payment_method.required' => '支払い方法を選択してください',
            'payment_method.in' => '支払い方法の形式が不正です',
        ]);

        // 配送先（profilesから）
        $address = Profile::where('user_id', $user->id)->first();

        // 住所がない場合は住所変更へ（要件に合わせて調整OK）
        if (!$address) {
            return redirect()->route('purchase.address')
                ->with('error', '配送先住所を登録してください');
        }

        /**
         * 注文作成（あなたの orders テーブル構成に合わせてカラムは調整してください）
         * 例として buyer_user_id / item_id / payment_method / postal_code / address / price を想定
         */
        Order::create([
            'buyer_user_id'  => $user->id,
            'item_id'        => $item->id,
            'payment_method' => $validated['payment_method'],
            'postal_code'    => $address->postal_code,
            'address'        => $address->address,
            'price'          => $item->price,
        ]);

        // 在庫・購入済みフラグなどがあるならここで更新
        // $item->update(['is_sold' => true]);

        return redirect()->route('purchase.show', $item->id)
            ->with('success', '購入が完了しました');
    }
}
