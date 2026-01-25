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

        $profile = Profile::where('user_id', $user->id)->firstOrFail();

        $order = Order::create([
            'buyer_user_id' => $user->id,
            'item_id'       => $item->id,
            'postal_code'   => $profile->postal_code,
            'address'       => $profile->address,
            'building'      => $profile->building,
        ]);

        // コンビニ払い (Top画面へ)
        if ($method === 'konbini') {
            $item->update(['status' => 1]);

            return redirect()->route('top')
                ->with('success', '購入を受け付けました（コンビニ払い）。');
        }

        // カード払い (Stripe決済へ)
        Stripe::setApiKey(config('services.stripe.secret'));

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
}
