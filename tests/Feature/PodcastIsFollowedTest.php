<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\{Podcast, Role, User};
use Tests\TestCase;

class PodcastIsFollowedTest extends TestCase
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
        $response = $this->getJson(route('podcasts.is-followed', [
            'podcast' => 'non-existent-id'
        ]));

        $response->assertNotFound();
    }

    /**
     * Request was successful
     */
    public function test_request_was_successfully_fetched()
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
        $response = $this->getJson(route('podcasts.is-followed', [
            'podcast' => $podcast
        ]));

        $response->assertOk();
    }
}
