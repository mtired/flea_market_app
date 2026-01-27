<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class EmailVerificationFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'テスト',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ], $overrides);
    }

    /**
     * ■ID16-1
     * 会員登録後、認証メールが送信される
     */
    public function test_id16_1_verify_email_is_sent_after_register(): void
    {
        Notification::fake();

        $payload = $this->validPayload([
            'email' => 'success@example.com',
        ]);

        $this->post('/register', $payload);

        $user = User::where('email', 'success@example.com')->firstOrFail();

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    /**
     * ■ID16-2
     * メール認証誘導画面で「認証はこちらから」ボタンを押下するとメール認証サイトに遷移する
     */
    public function test_id16_2_click_verify_button_redirects_to_verification_site(): void
    {
        $user = User::factory()->unverified()->create();
        $this->actingAs($user);

        $response = $this->get('/email/verify');

        $response->assertStatus(200);
    }

    /**
     * ■ID16-3
     * メール認証サイトのメール認証を完了すると、プロフィール設定画面に遷移する
     */
    public function test_id16_3_after_email_verification_redirects_to_profile_setup(): void
    {
        $user = User::factory()->unverified()->create();
        $this->actingAs($user);

        // 認証URL（署名付き）を作成して、メール内リンクを踏んだのと同じ状態にする
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
            ]
        );

        $response = $this->get($verificationUrl);

        $response->assertRedirect('/mypage/profile');

        $this->assertNotNull($user->fresh()->email_verified_at);
    }
}
