<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\{Comment, Content, Role, User};
use Tests\TestCase;

class GetAContentCommentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Content not found
     */
    public function test_content_is_not_found()
    {        
        $response = $this->getJson(route('contents.show-comment', [
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
        $content = Content::factory()
                        ->create();
        
        $response = $this->getJson(route('contents.show-comment', [
            'content' => $content,
            'comment' => 'non-existent-id'
        ]));

        $response->assertNotFound();
    }

    /**
     * Content comment was successfully fetched
     */
    public function test_content_comment_was_successfully_fetched()
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

        $response = $this->getJson(route('contents.show-comment', [
            'content' => $content,
            'comment' => $comment
        ]));

        $response->assertOk();
    }
}
