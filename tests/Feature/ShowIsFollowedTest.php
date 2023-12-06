<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\{Show, Role, User};
use Tests\TestCase;

class ShowIsFollowedTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Show not found
     */
    public function test_show_is_not_found()
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
        $response = $this->getJson(route('shows.is-followed', [
            'show' => 'non-existent-id'
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
        $show = Show::factory()
                    ->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('shows.is-followed', [
            'show' => $show
        ]));

        $response->assertOk();
    }
}
