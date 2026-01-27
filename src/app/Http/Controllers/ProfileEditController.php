<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Profile;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ProfileEditRequest;

class ProfileEditController extends Controller
{
    /**
     * プロフィール編集ページ表示
     */
    public function edit()
    {
        $user = Auth::user();

        $profile = Profile::where('user_id', $user->id)->first();

        return view('profile_edit', compact('user', 'profile'));
    }

    /**
     * プロフィール更新
     */
    public function update(ProfileEditRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        $user->update([
            'name' => $validated['name'],
        ]);

        // 既存profile取得（古い画像削除用）
        $existing = Profile::where('user_id', $user->id)->first();
        $imagePath = $existing?->image;

        if ($request->hasFile('image')) {
            if ($existing?->image) {
                Storage::disk('public')->delete($existing->image);
            }

            $imagePath = $request->file('image')->store('profile_images', 'public');
        }

        $isCompleted =
            !empty($validated['postal_code']) &&
            !empty($validated['address']);

        // 「初回登録時のみ」profile_completed_at を入力
        $alreadyCompleted = !is_null($existing?->profile_completed_at);


        $data = [
            'postal_code' => $validated['postal_code'],
            'address'     => $validated['address'],
            'building'    => $validated['building'] ?? null,
            'image'       => $imagePath,
        ];

        if ($isCompleted && !$alreadyCompleted) {
            $data['profile_completed_at'] = now();
        }

        // profiles テーブル（初回は作成、2回目以降は更新）
        Profile::updateOrCreate(
            ['user_id' => $user->id],
            $data
        );

        return redirect()->route('mypage')->with('status', 'プロフィールを更新しました');
    }
}
