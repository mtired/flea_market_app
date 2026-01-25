<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemListTest extends TestCase
{
    use RefreshDatabase;

    private string $listUrl = '/'; // 商品一覧ページのURL。違うならここだけ変える

    /**
     * ■ID4-1
     * 全商品を取得できる
     */
    public function test_id4_1_can_get_all_items(): void
    {
        // 出品者を2人作る
        $seller1 = User::factory()->create();
        $seller2 = User::factory()->create();

        // 商品を3つ作る（購入済み/未購入混ぜてもOK）
        $itemA = Item::factory()->create(['user_id' => $seller1->id, 'name' => '商品A', 'status' => 0]);
        $itemB = Item::factory()->create(['user_id' => $seller2->id, 'name' => '商品B', 'status' => 0]);
        $itemC = Item::factory()->create(['user_id' => $seller2->id, 'name' => '商品C', 'status' => 1]);

        $response = $this->get($this->listUrl);

        $response->assertStatus(200);

        // 一覧に商品名が表示されること（最低限の確認）
        $response->assertSee($itemA->name);
        $response->assertSee($itemB->name);
        $response->assertSee($itemC->name);
    }

    /**
     * ■ID4-2
     * 購入済み商品は「Sold」と表示される
     */
    public function test_id4_2_sold_item_shows_sold_label(): void
    {
        $seller = User::factory()->create();

        $soldItem = Item::factory()->create([
            'user_id' => $seller->id,
            'name' => '購入済み商品',
            'status' => 1,
        ]);

        $normalItem = Item::factory()->create([
            'user_id' => $seller->id,
            'name' => '未購入商品',
            'status' => 0,
        ]);

        $response = $this->get($this->listUrl);

        $response->assertStatus(200);

        $response->assertSee($soldItem->name);
        $response->assertSee($normalItem->name);

        $response->assertSee('Sold');
    }

    /**
     * ■ID4-3
     * 自分が出品した商品は表示されない
     */
    public function test_id4_3_my_items_are_not_listed_when_logged_in(): void
    {
        $me = User::factory()->create();
        $other = User::factory()->create();

        $myItem = Item::factory()->create([
            'user_id' => $me->id,
            'name' => '自分の商品',
            'status' => 0,
        ]);

        $otherItem = Item::factory()->create([
            'user_id' => $other->id,
            'name' => '他人の商品',
            'status' => 0,
        ]);

        $response = $this->actingAs($me)->get($this->listUrl);

        $response->assertStatus(200);

        $response->assertDontSee($myItem->name);

        $response->assertSee($otherItem->name);
    }
}