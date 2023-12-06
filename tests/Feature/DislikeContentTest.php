<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\{Content, Role, User};
use Tests\TestCase;

class DislikeContentTest extends TestCase
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
        $response = $this->postJson(route('contents.dislike', [
            'content' => 'non-existent-id'
        ]));

        $response->assertNotFound();
    }

    /**
     * Content was successfully disliked
     */
    public function test_content_was_successfully_disliked()
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
        $user->likes()
            ->toggle($content);
        
        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('contents.dislike', [
            'content' => $content
        ]));

        $response->assertOk();
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'content_id' => $content->id
        ]);
    }
}
