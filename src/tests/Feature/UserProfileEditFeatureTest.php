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
            'image' => 'https://example.com/profile.jpg',
            'profile_completed_at' => now(),
        ]);

        return $user;
    }

    /**
     * プロフィール編集画面のURL
     */
    private function profileEditUrl(): string
    {
        // 例：ミドルウェアで飛ばしている route('profile.edit') があるなら：
        // return route('profile.edit');

        // 例：/mypage/profile が編集画面なら：
        return '/mypage/profile';

        // 例：/mypage/profile/edit の場合：
        // return '/mypage/profile/edit';
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

        // ユーザー名（input value に入っている想定）
        $res->assertSee('value="山田テスト"', false);

        // 郵便番号
        $res->assertSee('value="123-4567"', false);

        // 住所
        $res->assertSee('value="東京都テスト区1-2-3"', false);

        // プロフィール画像
        // 表示の仕方が2パターンあるので、どちらかに当たる想定で両方見る（片方だけでもOK）
        // 1) <img src="...">
        $res->assertSee('https://example.com/profile.jpg', false);

        // もし storage 経由で加工されるなら、上が落ちる可能性があります。
        // その場合は profile_edit.blade.php の img 部分に合わせて assert を変更してください。
    }
}