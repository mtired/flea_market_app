<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Condition;
use App\Models\Item;
use App\Models\Like;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    private function makeCompletedUser(array $userAttrs = []): User
    {
        $user = User::factory()->create($userAttrs);
        Profile::factory()->completed()->create(['user_id' => $user->id]);
        return $user;
    }

    private function detailUrl(Item $item): string
    {
        return route('items.show', ['item' => $item->id]);
    }

    /**
     * ■ID7-1
     * 商品詳細ページに必要な情報が表示される
     *
     * 表示対象：
     * - 商品画像（image_url）
     * - 商品名 / ブランド / 価格
     * - 状態（Condition名）
     * - カテゴリ（複数）
     * - いいね数 / コメント数
     * - コメント本文＆投稿者名
     */
    public function test_id7_1_item_detail_shows_all_required_information(): void
    {
        $viewer = $this->makeCompletedUser(['name' => '閲覧ユーザー']);
        $seller = $this->makeCompletedUser(['name' => '出品者']);

        $condition = Condition::factory()->create([
            'name' => '新品',
        ]);

        // カテゴリ：家電 / スマホ
        $cat1 = Category::create(['name' => '家電']);
        $cat2 = Category::create(['name' => 'スマホ']);

        $item = Item::factory()->create([
            'user_id'      => $seller->id,
            'condition_id' => $condition->id,
            'status'       => 0,
            'name'         => 'テスト商品名',
            'brand'        => 'テストブランド',
            'description'  => 'テスト説明文です',
            'price'        => 12000,
            'image'        => 'https://example.com/test.jpg',
        ]);

        // カテゴリ紐付け
        $item->categories()->attach([$cat1->id, $cat2->id]);

        Like::create([
            'item_id' => $item->id,
            'user_id' => $viewer->id,
        ]);

        Comment::create([
            'item_id' => $item->id,
            'user_id' => $viewer->id,
            'content' => 'テストコメントです',
        ]);

        // 商品詳細を表示（ログイン状態）
        $response = $this->actingAs($viewer)->get($this->detailUrl($item));
        $response->assertStatus(200);

        // 基本情報
        $response->assertSee('テスト商品名');
        $response->assertSee('テストブランド');
        $response->assertSee('12,000');

        // 商品画像
        $response->assertSee($item->image_url, false);

        // 状態・カテゴリ
        $response->assertSee('新品');
        $response->assertSee('家電');
        $response->assertSee('スマホ');

        // いいね数・コメント数
        $response->assertSee('1');

        // コメント本文・投稿者名
        $response->assertSee('テストコメントです');
        $response->assertSee('閲覧ユーザー');
    }
}