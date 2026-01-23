<?php

namespace Tests\Feature;

use App\Models\Condition;
use App\Models\Item;
use App\Models\Order;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseWithoutStripeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 商品状態（condition）作成
     */
    private function createCondition(): Condition
    {
        return Condition::create([
            'name' => '新品',
        ]);
    }

    /**
     * プロフィール完了済みユーザー作成
     */
    private function makeCompletedUser(): User
    {
        $user = User::factory()->create();

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
     * 購入画面URL（GET）
     */
    private function purchasePageUrl(Item $item): string
    {
        // routes/web.php に合わせて必要なら変更
        return "/purchase/{$item->id}";
    }

    /**
     * 購入処理URL（POST）
     */
    private function purchaseSubmitUrl(Item $item): string
    {
        // PurchaseController@store のURLに合わせる
        return "/purchase/{$item->id}";
    }

    /**
     * トップページURL
     */
    private function topUrl(): string
    {
        return route('top'); // なければ '/'
    }

    /**
     * 購入商品一覧（プロフィール）
     */
    private function buyListUrl(): string
    {
        return '/mypage?page=buy';
    }

    /**
     * ■ID10-1
     * 「購入する」ボタンを押下すると購入が完了する
     */
    public function test_id10_1_purchase_completes_without_stripe(): void
    {
        $buyer = $this->makeCompletedUser();
        $seller = $this->makeCompletedUser();
        $condition = $this->createCondition();

        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'condition_id' => $condition->id,
            'status' => 0,
            'price' => 1000,
            'name' => '購入テスト商品',
        ]);

        $this->actingAs($buyer)
            ->get($this->purchasePageUrl($item))
            ->assertStatus(200);

        $res = $this->actingAs($buyer)->post($this->purchaseSubmitUrl($item), [
            'payment_method' => 'convenience',
        ]);

        $res->assertRedirect($this->topUrl());

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'status' => 1,
        ]);

        $this->assertDatabaseHas('orders', [
            'buyer_user_id' => $buyer->id,
            'item_id' => $item->id,
            'postal_code' => '123-4567',
            'address' => '東京都テスト区1-2-3',
            'building' => 'テストビル',
        ]);
    }

    /**
     * ■ID10-2
     * 購入した商品は商品一覧画面にて「Sold」と表示される
     */
    public function test_id10_2_purchased_item_shows_sold_in_top_list(): void
    {
        $buyer = $this->makeCompletedUser();
        $seller = $this->makeCompletedUser();
        $condition = $this->createCondition();

        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'condition_id' => $condition->id,
            'status' => 0,
            'price' => 2000,
            'name' => 'SOLD表示テスト商品',
        ]);

        // 購入
        $this->actingAs($buyer)->post($this->purchaseSubmitUrl($item), [
            'payment_method' => 'convenience',
        ])->assertRedirect($this->topUrl());

        $this->assertSame(1, $item->fresh()->status);

        $top = $this->actingAs($buyer)->get($this->topUrl());
        $top->assertStatus(200);

        $top->assertSee('SOLD表示テスト商品');

        $top->assertSee('Sold');
    }

    /**
     * ■ID10-3
     * 「プロフィール / 購入した商品一覧」に追加されている
     */
    public function test_id10_3_purchased_item_appears_in_profile_buy_list(): void
    {
        $buyer = $this->makeCompletedUser();
        $seller = $this->makeCompletedUser();
        $condition = $this->createCondition();

        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'condition_id' => $condition->id,
            'status' => 0,
            'price' => 3000,
            'name' => '購入一覧表示テスト商品',
        ]);

        // 購入
        $this->actingAs($buyer)->post($this->purchaseSubmitUrl($item), [
            'payment_method' => 'convenience',
        ])->assertRedirect($this->topUrl());

        $this->assertDatabaseHas('orders', [
            'buyer_user_id' => $buyer->id,
            'item_id' => $item->id,
        ]);

        $res = $this->actingAs($buyer)->get($this->buyListUrl());
        $res->assertStatus(200);
        $res->assertSee('購入一覧表示テスト商品');
    }
}