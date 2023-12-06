<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\{Content, Role, User};
use Tests\TestCase;

class RemoveContentFromFavouritesTest extends TestCase
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
        $response = $this->postJson(route('contents.unfavourite', [
            'content' => 'non-existent-id'
        ]));

        $response->assertNotFound();
    }

    /**
     * Content was successfully removed from favourites
     */
    public function test_content_was_successfully_removed_from_favourites()
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
        $user->favourites()
            ->updateOrCreate([
                'content_id' => $content->id
            ]);
        
        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('contents.unfavourite', [
            'content' => $content
        ]));

        $response->assertOk();
        $this->assertDatabaseMissing('favourites', [
            'user_id' => $user->id,
            'content_id' => $content->id
        ]);
    }
}
