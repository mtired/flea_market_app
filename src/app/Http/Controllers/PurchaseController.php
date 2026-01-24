<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Profile;
use App\Models\Order;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;

class PurchaseController extends Controller
{
    /**
     * 商品購入ページ表示
     */
    public function show(Item $item)
    {
        $user = Auth::user();

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
            'payment_method' => ['required', 'in:card,konbini'],
        ]);

        $method = $validated['payment_method'];

        // 注文作成
        $order = Order::create([
            'buyer_user_id'  => $user->id,
            'item_id'        => $item->id,
            'postal_code'    => auth()->user()->profile->postal_code,
            'address'        => auth()->user()->profile->address,
            'building'        => auth()->user()->profile->building,
        ]);

        // Stripe APIキー
        Stripe::setApiKey(config('services.stripe.secret'));

        // Checkout セッション作成
        $session = \Stripe\Checkout\Session::create([
            'mode' => 'payment',
            'payment_method_types' =>  [$method],
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

        // 購入済みフラグ更新
        $item->update(['status' => 1]);

        // Stripeの決済画面へ
        return redirect()->away($session->url);

    }

    /**
     * Stipe決済キャンセル時
     */
    public function cancel(Request $request)
    {
        $orderId = $request->query('order');

        $order = Order::where('id', $orderId)
            ->where('buyer_user_id', auth()->id())
            ->firstOrFail();

        if ($order->item_id->status !== 1) {
            $order->update(['status' => 'canceled']);
        }

        return redirect('/')
            ->with('info', '購入をキャンセルしました。');
    }
}
