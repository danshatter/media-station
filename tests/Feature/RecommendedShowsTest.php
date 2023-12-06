<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\{Role, User};
use Tests\TestCase;

class RecommendedShowsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Recommended shows were successfully fetched
     */
    public function test_recommended_shows_were_successfully_fetched()
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
        $response = $this->getJson(route('recommended-shows'));

        $response->assertOk();
    }
}
