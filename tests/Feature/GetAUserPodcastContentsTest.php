<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\{Podcast, Role, User};
use Tests\TestCase;

class GetAUserPodcastContentsTest extends TestCase
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
        $response = $this->getJson(route('podcasts.user-contents', [
            'podcast' => 'non-existent-id'
        ]));

        $response->assertNotFound();
    }

    /**
     * Podcast contents fetched successfully
     */
    public function test_user_podcast_contents_were_successfully_fetched()
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
        $user->podcasts()
            ->toggle($podcast);

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('podcasts.user-contents', [
            'podcast' => $podcast
        ]));

        $response->assertOk();
    }
}
