<?php

namespace Tests\Feature;

use App\Models\Condition;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ★あなたの検索パラメータ名に合わせて変更してください
     */
    private string $searchParam = 'keyword';

    /**
     * ■ID6-1
     * 「商品名」で部分一致検索ができる
     */
    public function test_id6_1_can_partial_match_search_by_item_name(): void
    {
        $seller = User::factory()->create();

        $hit = Item::factory()->create([
            'user_id' => $seller->id,
            'name' => 'Apple iPhone 15',
            'status' => 0,
        ]);

        $miss = Item::factory()->create([
            'user_id' => $seller->id,
            'name' => 'Nintendo Switch',
            'status' => 0,
        ]);

        $response = $this->get('/?'.$this->searchParam.'=iPho');

        $response->assertStatus(200);
        $response->assertSee($hit->name);
        $response->assertDontSee($miss->name);
    }

    /**
     * ■ID6-2
     * 検索状態がマイリストでも保持されている
     *
     * 1) マイリスト遷移後も、検索キーワードがHTML（inputのvalue等）に残っている
     * 2) その検索キーワードで、マイリスト表示も絞り込まれている
     */
    public function test_id6_2_search_keyword_is_kept_when_moving_to_mylist_tab(): void
    {
        $me = User::factory()->create();
        $seller = User::factory()->create();

        // いいね対象：検索でヒットする
        $likedHit = Item::factory()->create([
            'user_id' => $seller->id,
            'name' => 'Apple Watch',
            'status' => 0,
        ]);

        // いいね対象：検索でヒットしない
        $likedMiss = Item::factory()->create([
            'user_id' => $seller->id,
            'name' => 'Camera',
            'status' => 0,
        ]);

        // 自分が「いいね」した商品にする
        $likedHit->likedUsers()->attach($me->id);
        $likedMiss->likedUsers()->attach($me->id);

        $keyword = 'Apple';

        // ホームで検索
        $responseHome = $this->actingAs($me)->get('/?'.$this->searchParam.'='.$keyword);
        $responseHome->assertStatus(200);
        $responseHome->assertSee($likedHit->name);
        $responseHome->assertDontSee($likedMiss->name);

        // マイリストへ遷移
        $responseMylist = $this->actingAs($me)->get('/?tab=mylist&'.$this->searchParam.'='.$keyword);

        $responseMylist->assertStatus(200);

        $responseMylist->assertSee('value="'.$keyword.'"', false);

        $responseMylist->assertSee($likedHit->name);
        $responseMylist->assertDontSee($likedMiss->name);
    }
}