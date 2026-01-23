<?php

namespace Tests\Feature;

use App\Models\Condition;
use App\Models\Item;
use App\Models\Order;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressEditFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function createCondition(): Condition
    {
        return Condition::create(['name' => '新品']);
    }

    /**
     * プロフィール完了済みユーザー（住所も一旦入れておく）
     * ※ EnsureProfileCompleted を考慮
     */
    private function makeCompletedUser(): User
    {
        $user = User::factory()->create();

        Profile::create([
            'user_id' => $user->id,
            'postal_code' => '000-0000',
            'address' => '初期住所',
            'building' => null,
            'image' => 'https://example.com/profile.jpg',
            'profile_completed_at' => now(),
        ]);

        return $user;
    }

    private function addressEditUrl(Item $item): string
    {
        // 例：/purchase/address/{item_id}
        return "/purchase/address/{$item->id}";
        // ルート名があるなら：return route('purchase.address.edit', ['item' => $item->id]);
    }

    private function addressUpdateUrl(Item $item): string
    {
        // 例：PUT /purchase/address/{item_id}
        return "/purchase/address/{$item->id}";
        // ルート名があるなら：return route('purchase.address.update', ['item' => $item->id]);
    }

    private function purchasePageUrl(Item $item): string
    {
        return "/purchase/{$item->id}";
        // ルート名があるなら：return route('purchase.show', ['item' => $item->id]);
    }

    private function purchaseSubmitUrl(Item $item): string
    {
        return "/purchase/{$item->id}";
        // ルート名があるなら：return route('purchase.store', ['item' => $item->id]);
    }

    /**
     * ■ID12-1
     * 住所変更画面で登録した住所が購入画面に反映される
     */
    public function test_id12_1_updated_address_is_reflected_on_purchase_page(): void
    {
        $buyer = $this->makeCompletedUser();
        $seller = $this->makeCompletedUser();
        $condition = $this->createCondition();

        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'condition_id' => $condition->id,
            'status' => 0,
            'name' => '住所反映テスト商品',
            'price' => 1000,
        ]);

        // 1. ログイン
        // 2. 住所変更画面で住所を登録（PUT）
        $new = [
            'postal_code' => '123-4567',
            'address' => '東京都テスト区1-2-3',
            'building' => 'テストビル',
        ];

        $res = $this->actingAs($buyer)
            ->from($this->addressEditUrl($item))
            ->put($this->addressUpdateUrl($item), $new);

        // リダイレクト先は実装次第なので「リダイレクトした」だけ確認（必要ならURLも固定）
        $res->assertStatus(302);

        // DBのprofileが更新されている
        $this->assertDatabaseHas('profiles', [
            'user_id' => $buyer->id,
            'postal_code' => '123-4567',
            'address' => '東京都テスト区1-2-3',
            'building' => 'テストビル',
        ]);

        // 3. 購入画面を再度開く → 住所が反映されている
        $purchase = $this->actingAs($buyer)->get($this->purchasePageUrl($item));
        $purchase->assertStatus(200);

        // 購入画面の表示内容はBladeに依存するので、
        // とりあえず “住所文字列が含まれる” で検証（必要ならHTML構造に合わせて強化OK）
        $purchase->assertSee('123-4567');
        $purchase->assertSee('東京都テスト区1-2-3');
        $purchase->assertSee('テストビル');
    }

    /**
     * ■ID12-2
     * 購入した商品に送付先住所が紐づいて登録される（ordersに保存される）
     */
    public function test_id12_2_address_is_saved_to_order_on_purchase(): void
    {
        $buyer = $this->makeCompletedUser();
        $seller = $this->makeCompletedUser();
        $condition = $this->createCondition();

        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'condition_id' => $condition->id,
            'status' => 0,
            'name' => '住所紐づけテスト商品',
            'price' => 2000,
        ]);

        // 1. ログイン
        // 2. 住所変更
        $new = [
            'postal_code' => '987-6543',
            'address' => '大阪府テスト市4-5-6',
            'building' => 'テストマンション101',
        ];

        $this->actingAs($buyer)
            ->put($this->addressUpdateUrl($item), $new)
            ->assertStatus(302);

        // 3. 商品を購入（PurchaseControllerのバリデーションに合わせて payment_method 必須）
        $buy = $this->actingAs($buyer)->post($this->purchaseSubmitUrl($item), [
            'payment_method' => 'convenience',
        ]);

        // 購入後の遷移先は top など。固定できるなら assertRedirect(route('top')) にしてOK
        $buy->assertStatus(302);

        // 期待：ordersに住所が保存されている
        $this->assertDatabaseHas('orders', [
            'buyer_user_id' => $buyer->id,
            'item_id' => $item->id,
            'postal_code' => '987-6543',
            'address' => '大阪府テスト市4-5-6',
            'building' => 'テストマンション101',
        ]);

        // ついでに：itemがSOLDになっている（購入成功の裏取り）
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'status' => 1,
        ]);
    }
}