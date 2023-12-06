<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\{Role, User};
use Tests\TestCase;

class GetFollowedShowsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Shows were successfully fetched
     */
    public function test_shows_were_successfully_fetched()
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
        $response = $this->getJson(route('shows.following'));

        $response->assertOk();
    }
}
