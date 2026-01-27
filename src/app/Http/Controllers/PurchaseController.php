<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Profile;
use App\Models\Order;
use Stripe\Stripe;

class PurchaseController extends Controller
{
    /**
     * 商品購入ページ表示
     */
    public function show(Item $item)
    {
        $user = Auth::user();

        // itemに紐づく住所があれば優先（最新）
        $order = Order::where('buyer_user_id', $user->id)
            ->where('item_id', $item->id)
            ->latest()
            ->first();

        if ($order) {
            $address = (object) [
                'postal_code' => $order->postal_code,
                'address'     => $order->address,
                'building'    => $order->building,
            ];
        } else {
            $address = Profile::where('user_id', $user->id)->first();
        }

        return view('purchase', compact('item', 'address'));
    }

    /**
     * 購入処理
     */
    public function store(PurchaseRequest $request, Item $item)
    {
        $user = Auth::user();
        $validated = $request->validated();
        $method = $validated['payment_method'];

        // 住所変更で先に orders を作っている想定。無ければ profile から作る
        $order = Order::where('buyer_user_id', $user->id)
            ->where('item_id', $item->id)
            ->latest()
            ->first();

        if (!$order) {
            $profile = Profile::where('user_id', $user->id)->firstOrFail();

            $order = Order::create([
                'buyer_user_id' => $user->id,
                'item_id'       => $item->id,
                'postal_code'   => $profile->postal_code,
                'address'       => $profile->address,
                'building'      => $profile->building,
                'status'        => 0
            ]);
        }

        // コンビニ払い：ここで購入確定
        if ($method === 'konbini') {
            $order->update([
                'status' => 1
            ]);

            $item->update(['status' => 1]); // SOLD

            return redirect()->route('top')
                ->with('success', '購入を受け付けました（コンビニ払い）。');
        }

        // カード払い：Stripe決済へ
        Stripe::setApiKey(config('services.stripe.secret'));

        $session = \Stripe\Checkout\Session::create([
            'mode' => 'payment',
            'payment_method_types' => [$method],
            'line_items' => [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => 'jpy',
                    'unit_amount' => (int) $item->price,
                    'product_data' => ['name' => $item->name],
                ],
            ]],
            'success_url' => url('/'),
            'cancel_url'  => route('purchase.show', $item),
        ]);

        $order->update(['status' => 1]);
        $item->update(['status' => 1]);

        return redirect()->away($session->url);
    }
}