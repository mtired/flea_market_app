<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterValidationTest extends TestCase
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
     * ■ID1-1
     * 名前が入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_id1_1_name_is_required(): void
    {
        $response = $this->from('/register')->post('/register', $this->validPayload([
            'name' => '',
        ]));

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['name']);

        $this->assertSame(
            'お名前を入力してください',
            session('errors')->first('name')
        );
    }

    /**
     * ■ID1-2
     * メールアドレスが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_id1_2_email_is_required(): void
    {
        $response = $this->from('/register')->post('/register', $this->validPayload([
            'email' => '',
        ]));

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['email']);

        $this->assertSame(
            'メールアドレスを入力してください',
            session('errors')->first('email')
        );
    }

    /**
     * ■ID1-3
     * パスワードが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_id1_3_password_is_required(): void
    {
        $response = $this->from('/register')->post('/register', $this->validPayload([
            'password' => '',
            'password_confirmation' => '',
        ]));

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['password']);

        $this->assertSame(
            'パスワードを入力してください',
            session('errors')->first('password')
        );
    }

    /**
     * ■ID1-4
     * パスワードが7文字以下の場合、バリデーションメッセージが表示される
     */
    public function test_id1_4_password_must_be_at_least_8_chars(): void
    {
        $response = $this->from('/register')->post('/register', $this->validPayload([
            'password' => '1234567', // 7文字
            'password_confirmation' => '1234567',
        ]));

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['password']);

        $this->assertSame(
            'パスワードは8文字以上で入力してください',
            session('errors')->first('password')
        );
    }

    /**
     * ■ID1-5
     * パスワードが確認用パスワードと一致しない場合、バリデーションメッセージが表示される
     */
    public function test_id1_5_password_confirmation_must_match(): void
    {
        $response = $this->from('/register')->post('/register', $this->validPayload([
            'password' => 'password123',
            'password_confirmation' => 'password999',
        ]));

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['password']);

        $this->assertSame(
            'パスワードと一致しません',
            session('errors')->first('password')
        );
    }

}
