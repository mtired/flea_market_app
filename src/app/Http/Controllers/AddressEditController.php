<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;
use App\Models\Order;
use App\Http\Requests\AddressEditRequest;
use App\Models\Item;
use Illuminate\Http\Request;

class AddressEditController extends Controller
{
    public function index()
    {
        return view('address_edit');
    }

    /**
     * 住所変更ページ表示
     */
    public function edit(Item $item)
    {
        $userId = Auth::id();

        $order = Order::where('buyer_user_id', $userId)
            ->where('item_id', $item->id)
            ->latest()
            ->first();

        if ($order) {
            $profile = (object) [
                'postal_code' => $order->postal_code,
                'address'     => $order->address,
                'building'    => $order->building,
            ];
        } else {
            $profile = Profile::where('user_id', $userId)->first();
        }

        return view('address_edit', compact('item', 'profile'));
    }

    /**
     * 住所更新
     */
    public function update(AddressEditRequest $request, Item $item)
    {
        $validated = $request->validated();

        Order::updateOrCreate(
            [
                'buyer_user_id' => Auth::id(),
                'item_id'       => $item->id,
            ],
            [
                'postal_code' => $validated['postal_code'],
                'address'     => $validated['address'],
                'building'    => $validated['building'] ?? null,
                'status'      => 0
            ]
        );

        return redirect()
            ->route('purchase.show', ['item' => $item->id])
            ->with('success', '住所を更新しました');
    }
}
