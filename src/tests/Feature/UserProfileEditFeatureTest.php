<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserProfileEditFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function makeCompletedUserWithProfile(): User
    {
        $user = User::factory()->create([
            'name' => '山田テスト',
        ]);

        Profile::factory()->completed()->create([
            'user_id' => $user->id,
            'postal_code' => '123-4567',
            'address' => '東京都テスト区1-2-3',
            'building' => 'テストビル',
            'image' => 'profile_images/test.jpg',
        ]);

        return $user;
    }

    /**
     * プロフィール編集画面URL
     */
    private function profileEditUrl(): string
    {
        return route('profile.edit');
    }

    /**
     * ■ID14-1
     * 変更項目が初期値として過去設定されていること
     * （プロフィール画像、ユーザー名、郵便番号、住所）
     */
    public function test_id14_1_edit_page_shows_initial_values(): void
    {
        $user = $this->makeCompletedUserWithProfile();

        $res = $this->actingAs($user)->get($this->profileEditUrl());
        $res->assertStatus(200);

        // ユーザー名
        $res->assertSee('value="山田テスト"', false);

        // 郵便番号
        $res->assertSee('value="123-4567"', false);

        // 住所
        $res->assertSee('value="東京都テスト区1-2-3"', false);

        // 建物名
        $res->assertSee('value="テストビル"', false);

        // 画像
        $profile = Profile::where('user_id', $user->id)->firstOrFail();
        $res->assertSee('/storage/' . $profile->image, false);
    }
}