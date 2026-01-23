<?php

namespace Tests\Feature;

use App\Models\Condition;
use App\Models\Item;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentMethodSubtotalTest extends TestCase
{
    use RefreshDatabase;

    private function createCondition(): Condition
    {
        return Condition::create(['name' => '新品']);
    }

    private function makeCompletedUser(): User
    {
        $user = User::factory()->create();

        Profile::create([
            'user_id' => $user->id,
            'postal_code' => '123-4567',
            'address' => '東京都テスト区',
            'building' => 'テストビル',
            'image' => 'https://example.com/profile.jpg',
            'profile_completed_at' => now(),
        ]);

        return $user;
    }

    /**
     * ★URLとパラメータ名はあなたの実装に合わせてここだけ調整してください
     */
    private function paymentPageUrl(Item $item): string
    {
        return "/purchase/{$item->id}";
    }

    private function paymentUpdateUrl(Item $item): string
    {
        return "/purchase/{$item->id}/payment";
    }

    private string $paymentMethodParam = 'payment_method'; // selectのnameに合わせる
    private string $cardValue = 'card';
    private string $konbiniValue = 'konbini';

    /**
     * ■ID11-1
     * 小計画面で変更が反映される
     *
     * PHPUnitで拾うため「選択→送信→小計画面再表示」で検証します。
     */
    public function test_id11_1_subtotal_reflects_selected_payment_method(): void
    {
        $buyer = $this->makeCompletedUser();
        $seller = $this->makeCompletedUser();
        $condition = $this->createCondition();

        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'condition_id' => $condition->id,
            'price' => 1000,
            'status' => 0,
        ]);

        // ① 支払い方法を「カード」にして送信
        $res1 = $this->actingAs($buyer)->post($this->paymentUpdateUrl($item), [
            $this->paymentMethodParam => $this->cardValue,
        ]);

        // 実装により redirect するはず（小計画面に戻る等）
        $res1->assertRedirect();

        // 小計画面に反映されている（表示文言はあなたの画面に合わせて調整）
        $page1 = $this->actingAs($buyer)->get($this->paymentPageUrl($item));
        $page1->assertStatus(200);

        // 例：小計画面に「カード払い」が出るならこう
        $page1->assertSee('カード');

        // ② 支払い方法を「コンビニ」にして送信
        $res2 = $this->actingAs($buyer)->post($this->paymentUpdateUrl($item), [
            $this->paymentMethodParam => $this->konbiniValue,
        ]);
        $res2->assertRedirect();

        // 小計画面が「コンビニ」に更新されている
        $page2 = $this->actingAs($buyer)->get($this->paymentPageUrl($item));
        $page2->assertStatus(200);
        $page2->assertSee('コンビニ');
    }
}