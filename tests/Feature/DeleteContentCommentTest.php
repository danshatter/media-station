<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\{Comment, Content, Role, User};
use App\Exceptions\ForbiddenException;
use Tests\TestCase;

class DeleteContentCommentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Content not found
     */
    public function test_content_is_not_found()
    {
        $userRole = Role::factory()
                        ->user()
                        ->create();
        $adminRole = Role::factory()
                        ->administrator()
                        ->create();
        $user = User::factory()
                    ->users()
                    ->create();
        
        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('contents.destroy-comment', [
            'content' => 'non-existent-id',
            'comment' => 'non-existent-id'
        ]));

        $response->assertNotFound();
    }

    /**
     * Comment not found
     */
    public function test_comment_is_not_found()
    {
        $userRole = Role::factory()
                        ->user()
                        ->create();
        $adminRole = Role::factory()
                        ->administrator()
                        ->create();
        $user = User::factory()
                    ->users()
                    ->create();
        $content = Content::factory()
                        ->create();
        
        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('contents.destroy-comment', [
            'content' => $content,
            'comment' => 'non-existent-id'
        ]));

        $response->assertNotFound();
    }

    /**
     * Deleting of content comment forbidden
     */
    public function test_deleting_content_comment_is_forbidden()
    {
        $this->withoutExceptionHandling();
        $this->expectException(ForbiddenException::class);
        $userRole = Role::factory()
                        ->user()
                        ->create();
        $adminRole = Role::factory()
                        ->administrator()
                        ->create();
        $user = User::factory()
                    ->users()
                    ->create();
        $user2 = User::factory()
                    ->users()
                    ->create();
        $content = Content::factory()
                        ->create();
        $comment = Comment::factory()
                        ->for($content)
                        ->for($user2)
                        ->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('contents.destroy-comment', [
            'content' => $content,
            'comment' => $comment
        ]));
    }

    /**
     * Content comment was successfully deleted
     */
    public function test_content_comment_was_successfully_deleted()
    {
        $userRole = Role::factory()
                    ->user()
                    ->create();
        $adminRole = Role::factory()
                        ->administrator()
                        ->create();
        $user = User::factory()
                    ->users()
                    ->create();
        $content = Content::factory()
                        ->create();
        $comment = Comment::factory()
                        ->for($content)
                        ->for($user)
                        ->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('contents.destroy-comment', [
            'content' => $content,
            'comment' => $comment
        ]));

        $response->assertOk();
        $this->assertModelMissing($comment);
    }
}
