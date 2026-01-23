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

        Profile::create([
            'user_id' => $user->id,
            'postal_code' => '123-4567',
            'address' => '東京都テスト区1-2-3',
            'building' => 'テストビル',
            'image' => 'profile_images/test.jpg',
            'profile_completed_at' => now(),
        ]);

        return $user;
    }

    /**
     * プロフィール編集画面URL
     * ※必要に応じて修正
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

        // --- 画像（storage経由 or そのまま、どちらでも通るように） ---
        $content = $res->getContent();

        $this->assertTrue(
            str_contains($content, '/storage/profile_images/test.jpg')
            || str_contains($content, 'profile_images/test.jpg'),
            'プロフィール画像のパスが画面に表示されていません'
        );
    }
}