<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Condition;
use App\Models\Item;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemCreateFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function makeCompletedUser(): User
    {
        $user = User::factory()->create([
            'name' => '出品者テスト',
        ]);

        Profile::create([
            'user_id' => $user->id,
            'postal_code' => '123-4567',
            'address' => '東京都テスト区1-2-3',
            'building' => 'テストビル',
            'image' => 'profile_images/test.jpg',
            'profile_completed_at' => now(),
        ]);

        return $user;
    }

    private function createCondition(): Condition
    {
        return Condition::create(['name' => '新品']);
    }

    private function createCategories(): array
    {
        $cat1 = Category::create(['name' => '家電']);
        $cat2 = Category::create(['name' => '本']);

        return [$cat1, $cat2];
    }

    /**
     * 出品画面URL
     */
    private function sellPageUrl(): string
    {
        return '/sell';
    }

    /**
     * 出品保存URL
     */
    private function sellStoreUrl(): string
    {
        return '/sell';
    }

    /**
     * ■ID15-1
     * 商品出品画面にて必要な情報が保存できること
     */
    public function test_id15_1_item_can_be_created_with_required_fields(): void
    {
        $user = $this->makeCompletedUser();
        $condition = $this->createCondition();
        [$cat1, $cat2] = $this->createCategories();

        // 出品画面が開ける
        $this->actingAs($user)->get($this->sellPageUrl())->assertStatus(200);

        // 出品POST
        $payload = [
            'name' => 'テスト出品商品',
            'brand' => 'テストブランド',
            'description' => 'テスト説明です',
            'condition_id' => $condition->id,
            'price' => 12345,

            // ★ 相対パスで画像を保存
            'image' => 'item_images/test.jpg',

            // カテゴリ（フォーム名に合わせて調整）
            'category_id' => [$cat1->id, $cat2->id],
        ];

        $res = $this->actingAs($user)->post($this->sellStoreUrl(), $payload);
        $res->assertStatus(302);

        // items に保存されている
        $this->assertDatabaseHas('items', [
            'user_id' => $user->id,
            'name' => 'テスト出品商品',
            'brand' => 'テストブランド',
            'description' => 'テスト説明です',
            'condition_id' => $condition->id,
            'price' => 12345,
            'image' => 'item_images/test.jpg',
        ]);

        // item を取得
        $item = Item::where('user_id', $user->id)
            ->where('name', 'テスト出品商品')
            ->firstOrFail();

        // 中間テーブル（item_categories）
        $this->assertDatabaseHas('item_categories', [
            'item_id' => $item->id,
            'category_id' => $cat1->id,
        ]);
        $this->assertDatabaseHas('item_categories', [
            'item_id' => $item->id,
            'category_id' => $cat2->id,
        ]);

        // ★ accessor の確認（任意だけど強い）
        $this->assertSame('/storage/item_images/test.jpg', $item->image_url);
    }
}