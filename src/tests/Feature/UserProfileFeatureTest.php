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

    private function createCondition(): Condition
    {
        return Condition::create(['name' => '新品']);
    }

    private function makeCompletedUser(array $overrides = []): User
    {
        $user = User::factory()->create(array_merge([
            'name' => 'テスト太郎',
        ], $overrides));

        Profile::create([
            'user_id' => $user->id,
            'postal_code' => '123-4567',
            'address' => '東京都テスト区1-2-3',
            'building' => 'テストビル',
            // 画像はURLでもパスでもOK。ビューがその文字列を使うなら assertSee で拾える
            'image' => 'https://example.com/profile.jpg',
            'profile_completed_at' => now(),
        ]);

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
        $condition = $this->createCondition();

        // ログインユーザー
        $me = $this->makeCompletedUser([
            'name' => '山田テスト',
        ]);

        // 出品商品（自分がseller）
        $sellItem1 = Item::factory()->create([
            'user_id' => $me->id,
            'condition_id' => $condition->id,
            'status' => 0,
            'name' => '出品テスト商品A',
            'price' => 1000,
        ]);

        $sellItem2 = Item::factory()->create([
            'user_id' => $me->id,
            'condition_id' => $condition->id,
            'status' => 0,
            'name' => '出品テスト商品B',
            'price' => 2000,
        ]);

        // 購入商品（別sellerの商品を自分が購入）
        $seller = $this->makeCompletedUser(['name' => '出品者']);
        $buyItem = Item::factory()->create([
            'user_id' => $seller->id,
            'condition_id' => $condition->id,
            'status' => 1, // 購入済み想定（表示条件に使ってるなら重要）
            'name' => '購入テスト商品C',
            'price' => 3000,
        ]);

        // 購入一覧が Order ベースの場合に備えて注文作成（あなたのorders構造に合わせる）
        Order::create([
            'buyer_user_id' => $me->id,
            'item_id' => $buyItem->id,
            'postal_code' => '123-4567',
            'address' => '東京都テスト区1-2-3',
            'building' => 'テストビル',
        ]);

        // --- 出品一覧（/mypage?page=sell） ---
        $sellPage = $this->actingAs($me)->get($this->mypageSellUrl());
        $sellPage->assertStatus(200);

        // プロフィール画像・ユーザー名
        $sellPage->assertSee('山田テスト');
        $sellPage->assertSee('https://example.com/profile.jpg', false);

        // 出品した商品が表示される
        $sellPage->assertSee('出品テスト商品A');
        $sellPage->assertSee('出品テスト商品B');

        // --- 購入一覧（/mypage?page=buy） ---
        $buyPage = $this->actingAs($me)->get($this->mypageBuyUrl());
        $buyPage->assertStatus(200);

        // プロフィール画像・ユーザー名（タブ切り替えても表示される想定）
        $buyPage->assertSee('山田テスト');
        $buyPage->assertSee('https://example.com/profile.jpg', false);

        // 購入した商品が表示される
        $buyPage->assertSee('購入テスト商品C');
    }
}