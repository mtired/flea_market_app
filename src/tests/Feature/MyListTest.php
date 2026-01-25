<?php

namespace Tests\Feature;

use App\Models\Condition;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MyListTest extends TestCase
{
    use RefreshDatabase;

    private string $mylistUrl = '/?tab=mylist';

    /**
     * ■ID5-1
     * いいねした商品だけが表示される
     */
    public function test_id5_1_only_liked_items_are_shown(): void
    {
        $me = User::factory()->create();
        $seller = User::factory()->create();

        $likedItem = Item::factory()->create([
            'user_id' => $seller->id,
            'name' => 'いいね商品',
        ]);

        $notLikedItem = Item::factory()->create([
            'user_id' => $seller->id,
            'name' => 'いいねしてない商品',
        ]);

        // likes（中間テーブル）に「いいね」を作成
        $likedItem->likedUsers()->attach($me->id);

        $response = $this->actingAs($me)->get($this->mylistUrl);

        $response->assertStatus(200);
        $response->assertSee('いいね商品');
        $response->assertDontSee('いいねしてない商品');
    }

    /**
     * ■ID5-2
     * 購入済み商品は「Sold」と表示される
     */
    public function test_id5_2_sold_item_shows_sold_label(): void
    {
        $me = User::factory()->create();
        $seller = User::factory()->create();

        $soldLikedItem = Item::factory()->create([
            'user_id' => $seller->id,
            'name' => 'SOLDいいね商品',
            'status' => 1, // SOLD
        ]);

        $soldLikedItem->likedUsers()->attach($me->id);

        $response = $this->actingAs($me)->get($this->mylistUrl);

        $response->assertStatus(200);
        $response->assertSee('SOLDいいね商品');
        $response->assertSee('Sold');
    }

    /**
     * ■ID5-3
     * 未認証（未ログイン）の場合は何も表示されない
     */
    public function test_id5_3_guest_sees_nothing(): void
    {
        $me = User::factory()->create();
        $seller = User::factory()->create();

        $likedItem = Item::factory()->create([
            'user_id' => $seller->id,
            'name' => 'ゲストには見せない商品',
        ]);

        $likedItem->likedUsers()->attach($me->id);

        $response = $this->get($this->mylistUrl);

        $response->assertStatus(200);

        $response->assertDontSee('ゲストには見せない商品');
    }
}