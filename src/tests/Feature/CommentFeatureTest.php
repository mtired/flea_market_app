<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Item;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function makeCompletedUser(): User
    {
        $user = User::factory()->create();

        Profile::factory()->completed()->create(['user_id' => $user->id]);

        return $user;
    }

    private function detailUrl(Item $item): string
    {
        return route('items.show', ['item' => $item->id]);
    }

    private function commentPostUrl(Item $item): string
    {
        return route('items.comments.store', ['item' => $item->id]);
    }

    /**
     * ■ID9-1
     * ログイン済みのユーザーはコメントを送信できる
     */
    public function test_id9_1_logged_in_user_can_post_comment(): void
    {
        $user = $this->makeCompletedUser();
        $seller = $this->makeCompletedUser();

        $item = Item::factory()->create([
            'user_id' => $seller->id,
        ]);

        $this->assertSame(0, Comment::count());

        $response = $this->actingAs($user)
            ->from($this->detailUrl($item))
            ->post($this->commentPostUrl($item), [
                'content' => 'テストコメント',
            ]);

        $response->assertRedirect($this->detailUrl($item));

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => 'テストコメント',
        ]);

        $this->assertSame(1, Comment::count());
    }

    /**
     * ■ID9-2
     * ログイン前のユーザーはコメントを送信できない
     */
    public function test_id9_2_guest_cannot_post_comment(): void
    {
        $seller = $this->makeCompletedUser();

        $item = Item::factory()->create([
            'user_id' => $seller->id,
        ]);

        // 未ログインで投稿
        $response = $this->post($this->commentPostUrl($item), [
            'content' => 'ゲストコメント',
        ]);

        // authミドルウェアで login に飛ばされる想定
        $response->assertRedirect('/login');

        // コメントは保存されない
        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'content' => 'ゲストコメント',
        ]);
        $this->assertSame(0, Comment::count());
    }

    /**
     * ■ID9-3
     * コメントが未入力の場合、バリデーションメッセージが表示される
     */
    public function test_id9_3_comment_is_required(): void
    {
        $user = $this->makeCompletedUser();
        $seller = $this->makeCompletedUser();

        $item = Item::factory()->create([
            'user_id' => $seller->id,
        ]);

        $response = $this->actingAs($user)
            ->from($this->detailUrl($item))
            ->post($this->commentPostUrl($item), [
                'content' => '',
            ]);

        $response->assertRedirect($this->detailUrl($item));
        $response->assertSessionHasErrors('content');

        $this->assertSame(0, Comment::count());
    }

    /**
     * ■ID9-4
     * コメントが255字以上の場合、バリデーションメッセージが表示される
     */
    public function test_id9_4_comment_cannot_exceed_255_characters(): void
    {
        $user = $this->makeCompletedUser();
        $seller = $this->makeCompletedUser();

        $item = Item::factory()->create([
            'user_id' => $seller->id,
        ]);

        $longComment = str_repeat('あ', 256);

        $response = $this->actingAs($user)
            ->from($this->detailUrl($item))
            ->post($this->commentPostUrl($item), [
                'content' => $longComment,
            ]);

        $response->assertRedirect($this->detailUrl($item));
        $response->assertSessionHasErrors('content');

        $this->assertSame(0, Comment::count());
    }
}

