<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;
use App\Http\Requests\ProfileEditRequest;
use Symfony\Component\HttpKernel\Profiler\Profile as ProfilerProfile;

class ProfileEditController extends Controller
{
    public function index()
    {
        return view('profile_edit');
    }

    public function edit()
    {
        $user = Auth::user();

        $profile = Profile::where('user_id', $user->id)->first();

        return view('profile_edit', compact('user', 'profile'));
    }
    
    public function update(ProfileEditRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        // users テーブル（名前）
        $user->update([
            'name' => $validated['name'],
        ]);

        // 既存profile取得（古い画像削除用）
        $existing = Profile::where('user_id', $user->id)->first();
        $imagePath = $existing?->image;

        if ($request->hasFile('image')) {
            // 古い画像削除（任意）
            if ($existing?->image) {
                Storage::disk('public')->delete($existing->image);
            }

            $imagePath = $request->file('image')->store('profile_images', 'public');
        }

        // profiles テーブル（初回は作成、2回目以降は更新）
        Profile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'postal_code' => $validated['postal_code'],
                'address'     => $validated['address'],
                'building'    => $validated['building'] ?? null,
                'image'       => $imagePath, // ★ NOT NULL なので初回は必須にするのが安全
            ]
        );

        return redirect('/')->with('status', 'プロフィールを更新しました');
    }
}
