<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\{Role, Podcast, User};
use Tests\TestCase;

class GetAPodcastTest extends TestCase
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
        $admin = User::factory()
                    ->administrators()
                    ->create();
        
        Sanctum::actingAs($admin, ['*']);
        $response = $this->getJson(route('admin.podcasts.show', [
            'podcast' => 'non-existent-id'
        ]));

        $response->assertNotFound();
    }

    /**
     * Podcast was successfully fetched
     */
    public function test_podcast_was_successfully_fetched()
    {
        $userRole = Role::factory()
                        ->user()
                        ->create();
        $adminRole = Role::factory()
                        ->administrator()
                        ->create();
        $admin = User::factory()
                    ->administrators()
                    ->create();
        $podcast = Podcast::factory()
                        ->create();
        
        Sanctum::actingAs($admin, ['*']);
        $response = $this->getJson(route('admin.podcasts.show', [
            'podcast' => $podcast
        ]));

        $response->assertOk();
    }
}
