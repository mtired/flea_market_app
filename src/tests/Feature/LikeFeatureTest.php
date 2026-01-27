<?php

namespace Tests\Feature;

use App\Models\Condition;
use App\Models\Item;
use App\Models\Like;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikeFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function detailUrl(Item $item): string
    {
        return route('items.show', $item->id);
    }

    private function toggleLikeUrl(Item $item): string
    {
        return route('items.like.toggle', $item->id);
    }

    private function makeCompletedUser(): User
    {
        $user = User::factory()->create();

        Profile::factory()->completed()->create(['user_id' => $user->id]);

        return $user;
    }

    /**
     * ■ID8-1
     * いいねアイコンを押下することによって、いいねした商品として登録することができる。
     */
    public function test_id8_1_like_can_be_registered_and_count_increases(): void
    {
        $me = $this->makeCompletedUser();
        $seller = $this->makeCompletedUser();

        $item = Item::factory()->create([
            'user_id' => $seller->id,
        ]);

        $this->assertDatabaseMissing('likes', [
            'user_id' => $me->id,
            'item_id' => $item->id,
        ]);

        // いいね押下
        $res = $this->actingAs($me)->post($this->toggleLikeUrl($item));
        $res->assertRedirect();

        $this->assertDatabaseHas('likes', [
            'user_id' => $me->id,
            'item_id' => $item->id,
        ]);

        $detail = $this->actingAs($me)->get($this->detailUrl($item));
        $detail->assertStatus(200);

        $detail->assertSee('>1<', false);

        $detail->assertSee('images/ハートロゴ_ピンク.png', false);
    }

    /**
     * ■ID8-2
     * 追加済みのアイコンは色が変化する
     */
    public function test_id8_2_liked_icon_is_pink_when_already_liked(): void
    {
        $me = $this->makeCompletedUser();
        $seller = $this->makeCompletedUser();

        $item = Item::factory()->create([
            'user_id' => $seller->id,
        ]);

        Like::create([
            'user_id' => $me->id,
            'item_id' => $item->id,
        ]);

        $response = $this->actingAs($me)->get($this->detailUrl($item));
        $response->assertStatus(200);

        $response->assertSee('images/ハートロゴ_ピンク.png', false);

        $response->assertDontSee('images/ハートロゴ_デフォルト.png', false);
    }

    /**
     * ■ID8-3
     * 再度いいねアイコンを押下することによって、いいねを解除することができる。
     */
    public function test_id8_3_like_can_be_removed_and_count_decreases(): void
    {
        $me = $this->makeCompletedUser();
        $seller = $this->makeCompletedUser();

        $item = Item::factory()->create([
            'user_id' => $seller->id,
        ]);

        // いいね押下
        $likeRes = $this->actingAs($me)
            ->from($this->detailUrl($item))
            ->post($this->toggleLikeUrl($item));
        $likeRes->assertRedirect($this->detailUrl($item));

        $this->assertDatabaseHas('likes', [
            'user_id' => $me->id,
            'item_id' => $item->id,
        ]);

        // いいね解除
        $unlikeRes = $this->actingAs($me)
            ->from($this->detailUrl($item))
            ->post($this->toggleLikeUrl($item));
        $unlikeRes->assertRedirect($this->detailUrl($item));

        $this->assertDatabaseMissing('likes', [
            'user_id' => $me->id,
            'item_id' => $item->id,
        ]);

        // 表示確認
        $detail = $this->actingAs($me)->get($this->detailUrl($item));
        $detail->assertStatus(200);
        $detail->assertSee('>0<', false);
        $detail->assertSee('images/ハートロゴ_デフォルト.png', false);
    }
}