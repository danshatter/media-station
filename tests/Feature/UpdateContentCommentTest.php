<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\{Comment, Content, Role, User};
use App\Exceptions\ForbiddenException;
use Tests\TestCase;

class UpdateContentCommentTest extends TestCase
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
        $response = $this->putJson(route('contents.update-comment', [
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
        $response = $this->putJson(route('contents.update-comment', [
            'content' => $content,
            'comment' => 'non-existent-id'
        ]));

        $response->assertNotFound();
    }

    /**
     * Validation errors
     *
     * @return void
     */
    public function test_validation_errors_occur_while_updating_content_comment()
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
                        ->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->putJson(route('contents.update-comment', [
            'content' => $content,
            'comment' => $comment
        ]), [
            'body' => null
        ]);

        $response->assertInvalid(['body']);
        $response->assertUnprocessable();
    }

    /**
     * Updating of content comment forbidden
     */
    public function test_updating_content_comment_is_forbidden()
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
        $response = $this->putJson(route('contents.update-comment', [
            'content' => $content,
            'comment' => $comment
        ]), [
            'body' => fake()->sentence()
        ]);
    }

    /**
     * Content comment was successfully updated
     */
    public function test_content_comment_was_successfully_updated()
    {
        $oldComment = 'The content was a good one';
        $newComment = 'This never gets old';
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
                        ->create([
                            'body' => $oldComment
                        ]);

        Sanctum::actingAs($user, ['*']);
        $response = $this->putJson(route('contents.update-comment', [
            'content' => $content,
            'comment' => $comment
        ]), [
            'body' => $newComment
        ]);
        $comment->refresh();

        $response->assertOk();
        $this->assertSame($newComment, $comment->body);
    }
}
