<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\{Role, User};
use Tests\TestCase;

class GetFollowedPodcastsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Podcasts were successfully fetched
     */
    public function test_podcasts_were_successfully_fetched()
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
        $response = $this->getJson(route('podcasts.following'));

        $response->assertOk();
    }
}
