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

    private function makeCompletedUser(): User
    {
        $user = User::factory()->create();

        Profile::factory()->completed()->create(['user_id' => $user->id]);

        return $user;
    }

    private function paymentPageUrl(Item $item): string
    {
        return route('purchase.show', $item->id);
    }

    private function paymentUpdateUrl(Item $item): string
    {
        return route('purchase.store', $item->id);
    }

    private string $paymentMethodParam = 'payment_method';
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

        $item = Item::factory()->create([
            'user_id' => $seller->id,
        ]);

        // 支払い方法を「カード」にして送信
        $res1 = $this->actingAs($buyer)->post($this->paymentUpdateUrl($item), [
            $this->paymentMethodParam => $this->cardValue,
        ]);

        $res1->assertRedirect();

        // 小計画面表示
        $page1 = $this->actingAs($buyer)->get($this->paymentPageUrl($item));
        $page1->assertStatus(200);
        $page1->assertSee('カード');

        // 支払い方法を「コンビニ」にして送信
        $res2 = $this->actingAs($buyer)->post($this->paymentUpdateUrl($item), [
            $this->paymentMethodParam => $this->konbiniValue,
        ]);
        $res2->assertRedirect();

        // 小計画面表示
        $page2 = $this->actingAs($buyer)->get($this->paymentPageUrl($item));
        $page2->assertStatus(200);
        $page2->assertSee('コンビニ');
    }
}