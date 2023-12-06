<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\{Podcast, Role, User};
use Tests\TestCase;

class FollowPodcastTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Podcast not found
     */
    public function test_podcast_is_not_found()
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
        $response = $this->postJson(route('podcasts.user-follow', [
            'podcast' => 'non-existent-id'
        ]));

        $response->assertNotFound();
    }

    /**
     * User followed podcast successfully
     */
    public function test_user_successfully_followed_podcast()
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
        $podcast = Podcast::factory()
                        ->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('podcasts.user-follow', [
            'podcast' => $podcast
        ]));

        $response->assertOk();
        $this->assertDatabaseHas('podcast_user', [
            'user_id' => $user->id,
            'podcast_id' => $podcast->id
        ]);
    }
}
