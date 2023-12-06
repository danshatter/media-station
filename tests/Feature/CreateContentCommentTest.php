<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\{Content, Role, User};
use Tests\TestCase;

class CreateContentCommentTest extends TestCase
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
        $response = $this->postJson(route('contents.store-comment', [
            'content' => 'non-existent-id'
        ]));

        $response->assertNotFound();
    }

    /**
     * Validation errors
     *
     * @return void
     */
    public function test_validation_errors_occur_while_creating_content_comment()
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
        $response = $this->postJson(route('contents.store-comment', [
            'content' => $content
        ]), [
            'body' => null
        ]);

        $response->assertInvalid(['body']);
        $response->assertUnprocessable();
    }

    /**
     * Content comment was successfully created
     */
    public function test_content_comment_was_successfully_created()
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
        $response = $this->postJson(route('contents.store-comment', [
            'content' => $content
        ]), [
            'body' => fake()->sentence()
        ]);
        
        $response->assertValid();
        $response->assertCreated();
        $this->assertDatabaseHas('comments', [
            'content_id' => $content->id,
            'user_id' => $user->id
        ]);
    }
}
