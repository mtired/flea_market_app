<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Condition;
use App\Models\Item;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ItemCreateFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function makeCompletedUser(): User
    {
        $user = User::factory()->create([
            'name' => '出品者テスト',
        ]);

        Profile::factory()->completed()->create(['user_id' => $user->id]);

        return $user;
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
        return route('items.create');
    }

    /**
     * 出品保存URL
     */
    private function sellStoreUrl(): string
    {
        return route('items.store');
    }

    /**
     * ■ID15-1
     * 商品出品画面にて必要な情報が保存できること
     */
    public function test_id15_1_item_can_be_created_with_required_fields(): void
    {
        Storage::fake('public');

        $user = $this->makeCompletedUser();
        $condition = Condition::factory()->create();
        [$cat1, $cat2] = $this->createCategories();

        $this->actingAs($user)->get($this->sellPageUrl())->assertStatus(200);

        $payload = [
            'name' => 'テスト出品商品',
            'brand' => 'テストブランド',
            'description' => 'テスト説明です',
            'condition_id' => $condition->id,
            'price' => 12345,
            'image' => UploadedFile::fake()->create('test.jpeg', 100, 'image/jpeg'),
            'category_ids' => [$cat1->id, $cat2->id],
        ];

        $res = $this->actingAs($user)->post($this->sellStoreUrl(), $payload);

        $res->assertSessionHasNoErrors();

        $res->assertStatus(302);

        $this->assertDatabaseHas('items', [
            'user_id' => $user->id,
            'name' => 'テスト出品商品',
            'brand' => 'テストブランド',
            'description' => 'テスト説明です',
            'condition_id' => $condition->id,
            'price' => 12345,
            'status' => 0,
        ]);

        $item = Item::where('user_id', $user->id)
            ->where('name', 'テスト出品商品')
            ->firstOrFail();

        // 画像確認
        $this->assertStringStartsWith('item_images/', $item->image);
        Storage::disk('public')->assertExists($item->image);

        $this->assertDatabaseHas('item_categories', [
            'item_id' => $item->id,
            'category_id' => $cat1->id,
        ]);
        $this->assertDatabaseHas('item_categories', [
            'item_id' => $item->id,
            'category_id' => $cat2->id,
        ]);

        $this->assertSame('/storage/' . $item->image, $item->image_url);
    }
}