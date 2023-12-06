<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\{Content, Podcast, Role, User};
use Tests\TestCase;

class GetPodcastContentsTest extends TestCase
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
        $content = Content::factory()
                        ->create();
        
        Sanctum::actingAs($admin, ['*']);
        $response = $this->getJson(route('admin.podcasts.contents', [
            'podcast' => 'non-existent-id'
        ]));

        $response->assertNotFound();
    }

    /**
     * Podcast contents fetched successfully
     */
    public function test_podcast_contents_were_successfully_fetched()
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
        $content = Content::factory()
                        ->for($podcast, 'contentable')
                        ->create();

        Sanctum::actingAs($admin, ['*']);
        $response = $this->getJson(route('admin.podcasts.contents', [
            'podcast' => $podcast
        ]));

        $response->assertOk();
    }
}
