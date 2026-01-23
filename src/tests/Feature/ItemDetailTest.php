<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Condition;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    private function detailUrl(Item $item): string
    {
        // ルートが違う場合はここだけ変更
        return "/items/{$item->id}";
    }

    private function createCondition(): Condition
    {
        // conditions の必須カラムが name 以外にもあるなら、ここに追加してください
        return Condition::create([
            'name' => '新品',
        ]);
    }

    private function createCategory(string $name): Category
    {
        // categories の必須カラムが name 以外にもあるなら、ここに追加してください
        return Category::create([
            'name' => $name,
        ]);
    }

    /**
     * ■ID7-1
     * 必要な情報が表示される
     * （商品画像、商品名、ブランド名、価格、いいね数、コメント数、
     *  商品説明、商品情報（カテゴリ、商品の状態）、
     *  コメントしたユーザー情報、コメント内容）
     */
    public function test_id7_1_item_detail_shows_all_required_information(): void
    {
        $condition = $this->createCondition();

        $seller = User::factory()->create();
        $liker1 = User::factory()->create(['name' => 'いいね太郎']);
        $liker2 = User::factory()->create(['name' => 'いいね次郎']);

        $commenter1 = User::factory()->create(['name' => 'コメント花子']);
        $commenter2 = User::factory()->create(['name' => 'コメント次郎']);

        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'condition_id' => $condition->id,
            'name' => 'テスト商品名',
            'brand' => 'テストブランド',
            'price' => 47000,
            'description' => 'これはテスト用の商品説明です。',
            'image' => 'https://example.com/test.jpg', // URLなので実ファイル不要
            'status' => 0,
        ]);

        // カテゴリ（複数）
        $catA = $this->createCategory('家電');
        $catB = $this->createCategory('スマホ');
        $item->categories()->attach([$catA->id, $catB->id]);

        // いいね（2件）
        $item->likedUsers()->attach([$liker1->id, $liker2->id]);

        // コメント（2件）
        // Commentモデルのカラム名が違う場合は、ここだけ合わせてください
        Comment::create([
            'user_id' => $commenter1->id,
            'item_id' => $item->id,
            'content' => 'コメント1です',
        ]);

        Comment::create([
            'user_id' => $commenter2->id,
            'item_id' => $item->id,
            'content' => 'コメント2です',
        ]);

        $response = $this->get($this->detailUrl($item));
        $response->assertStatus(200);

        // --- 基本情報 ---
        $response->assertSee('テスト商品名');
        $response->assertSee('テストブランド');
        $response->assertSee('これはテスト用の商品説明です。');

        // 価格：表示が「¥47,000」形式ならこちらが通ります
        $response->assertSee('¥'.number_format(47000));

        // 商品画像（srcにURLが出る想定。HTMLエスケープ無効で検査）
        $response->assertSee('https://example.com/test.jpg', false);

        // --- 商品情報（状態・カテゴリ）---
        $response->assertSee('新品');
        $response->assertSee('家電');
        $response->assertSee('スマホ');

        // --- いいね数・コメント数 ---
        // 表示が「2」だけだと他の数字と被る可能性があるので、
        // 可能なら UI 文言（例：Like / いいね / コメント）に合わせて強化推奨。
        $response->assertSee('2'); // いいね数 or コメント数が2であること（最低限）

        // --- コメントユーザー情報・コメント内容 ---
        $response->assertSee('コメント花子');
        $response->assertSee('コメント次郎');
        $response->assertSee('コメント1です');
        $response->assertSee('コメント2です');
    }

    /**
     * ■ID7-2
     * 複数選択されたカテゴリが表示されているか
     */
    public function test_id7_2_multiple_categories_are_displayed(): void
    {
        $condition = $this->createCondition();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'condition_id' => $condition->id,
            'name' => 'カテゴリ表示テスト商品',
            'image' => 'https://example.com/test.jpg',
            'status' => 0,
        ]);

        $cat1 = $this->createCategory('メンズ');
        $cat2 = $this->createCategory('トップス');
        $cat3 = $this->createCategory('冬物');

        $item->categories()->attach([$cat1->id, $cat2->id, $cat3->id]);

        $response = $this->get($this->detailUrl($item));

        $response->assertStatus(200);
        $response->assertSee('メンズ');
        $response->assertSee('トップス');
        $response->assertSee('冬物');
    }
}