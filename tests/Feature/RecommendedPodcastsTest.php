<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\{Role, User};
use Tests\TestCase;

class RecommendedPodcastsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Recommended podcasts were successfully fetched
     */
    public function test_recommended_podcasts_were_successfully_fetched()
    {
        $userRole = Role::factory()
                    ->user()
                    ->create();
        $adminRole = Role::factory()
                        ->administrator()
                        ->create();
        $users = User::factory()
                    ->users()
                    ->create();

        Sanctum::actingAs($users, ['*']);
        $response = $this->getJson(route('recommended-podcasts'));

        $response->assertOk();
    }
}
