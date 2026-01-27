<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseWithoutStripeTest extends TestCase
{
    use RefreshDatabase;

    private function makeCompletedUser(): User
    {
        $user = User::factory()->create();

        Profile::factory()->completed()->create([
            'user_id' => $user->id,
            'postal_code' => '123-4567',
            'address' => '東京都テスト区1-2-3',
            'building' => 'テストビル',
        ]);

        return $user;
    }

    /**
     * 購入画面URL（GET）
     */
    private function purchasePageUrl(Item $item): string
    {
        return route('purchase.show', $item->id);
    }

    /**
     * 購入処理URL（POST）
     */
    private function purchaseSubmitUrl(Item $item): string
    {
        return route('purchase.store', $item->id);
    }

    /**
     * トップページURL
     */
    private function topUrl(): string
    {
        return route('top');
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

        $item = Item::factory()->create([
            'user_id' => $seller->id,
        ]);

        // 購入画面表示確認
        $this->actingAs($buyer)
            ->get($this->purchasePageUrl($item))
            ->assertStatus(200);

        // 購入
        $res = $this->actingAs($buyer)->post($this->purchaseSubmitUrl($item), [
            'payment_method' => 'konbini',
        ]);

        // トップ画面リダイレクト確認
        $res->assertRedirect($this->topUrl());

        // 購入確認（SOLD）
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'status' => 1,
        ]);

        // DB保存確認
        $this->assertDatabaseHas('orders', [
            'buyer_user_id' => $buyer->id,
            'item_id'       => $item->id,
            'postal_code'   => '123-4567',
            'address'       => '東京都テスト区1-2-3',
            'building'      => 'テストビル',
            'status'        => 1,
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

        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'name' => 'SOLD表示テスト商品',
        ]);

        // 購入
        $this->actingAs($buyer)->post($this->purchaseSubmitUrl($item), [
            'payment_method' => 'konbini',
        ])->assertRedirect($this->topUrl());

        // DB上でのSOLD確認
        $this->assertSame(1, $item->fresh()->status);

        // Top画面表示
        $top = $this->actingAs($buyer)->get($this->topUrl());
        $top->assertStatus(200);

        $top->assertSee('SOLD表示テスト商品');

        // Sold表示確認
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

        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'name' => '購入一覧表示テスト商品',
        ]);

        // 購入
        $this->actingAs($buyer)->post($this->purchaseSubmitUrl($item), [
            'payment_method' => 'konbini',
        ])->assertRedirect($this->topUrl());

        // orders 作成確認
        $this->assertDatabaseHas('orders', [
            'buyer_user_id' => $buyer->id,
            'item_id'       => $item->id,
            'status'        => 1,
        ]);

        // 商品一覧確認
        $res = $this->actingAs($buyer)->get($this->buyListUrl());
        $res->assertStatus(200);
        $res->assertSee('購入一覧表示テスト商品');
    }
}