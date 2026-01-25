<?php

namespace Tests\Feature;

use App\Models\Condition;
use App\Models\Item;
use App\Models\Order;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserProfileFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function makeCompletedUser(array $overrides = []): User
    {
        $user = User::factory()->create(array_merge([
            'name' => 'テスト太郎',
        ], $overrides));

        Profile::factory()->completed()->create(['user_id' => $user->id]);

        return $user;
    }

    private function mypageSellUrl(): string
    {
        return '/mypage?page=sell';
    }

    private function mypageBuyUrl(): string
    {
        return '/mypage?page=buy';
    }

    /**
     * ■ID13-1
     * 必要な情報が取得できる（プロフィール画像、ユーザー名、出品した商品一覧、購入した商品一覧）
     */
    public function test_id13_1_profile_page_shows_required_information(): void
    {
        // 出品商品
        $me = $this->makeCompletedUser(['name' => '山田テスト']);
        Item::factory()->create([
            'user_id' => $me->id,
            'status' => 0,
            'name' => '出品テスト商品A',
        ]);

        Item::factory()->create([
            'user_id' => $me->id,
            'status' => 0,
            'name' => '出品テスト商品B',
        ]);

        // 購入商品
        $seller = $this->makeCompletedUser(['name' => '出品者']);
        $buyItem = Item::factory()->create([
            'user_id' => $seller->id,
            'status' => 1,
            'name' => '購入テスト商品C',
        ]);

        Order::create([
            'buyer_user_id' => $me->id,
            'item_id' => $buyItem->id,
            'postal_code' => '123-4567',
            'address' => '東京都テスト区1-2-3',
            'building' => 'テストビル',
        ]);

        $profile = Profile::where('user_id', $me->id)->firstOrFail();

        // 出品一覧
        $sellPage = $this->actingAs($me)->get($this->mypageSellUrl());
        $sellPage->assertStatus(200);

        $sellPage->assertSee('山田テスト');

        if (!empty($profile->image)) {
            $sellPage->assertSee('/storage/' . $profile->image, false);
        }

        $sellPage->assertSee('出品テスト商品A');
        $sellPage->assertSee('出品テスト商品B');

        // 購入一覧
        $buyPage = $this->actingAs($me)->get($this->mypageBuyUrl());
        $buyPage->assertStatus(200);

        $buyPage->assertSee('山田テスト');

        if (!empty($profile->image)) {
            $buyPage->assertSee('/storage/' . $profile->image, false);
        }

        $buyPage->assertSee('購入テスト商品C');

    }
}