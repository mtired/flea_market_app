<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;
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
        $user = Auth::user();

        $profile = Profile::where('user_id', $user->id)->first();

        return view('address_edit', compact('item', 'profile'));
    }

    /**
     * 住所更新
     */
    public function update(AddressEditRequest $request, Item $item)
    {
        $validated = $request->validate([
            'postal_code' => ['required', 'string'],
            'address'     => ['required', 'string'],
            'building'    => ['nullable', 'string'],
        ]);

        Profile::updateOrCreate(
            ['user_id' => Auth::id()],
            $validated
        );

        // 購入画面に戻す
        return redirect()
            ->route('purchase.show', ['item' => $item->id])
            ->with('success', '住所を更新しました');
    }
}
