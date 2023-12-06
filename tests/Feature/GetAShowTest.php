<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\{Role, Show, User};
use Tests\TestCase;

class GetAShowTest extends TestCase
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
        $admin = User::factory()
                    ->administrators()
                    ->create();
        
        Sanctum::actingAs($admin, ['*']);
        $response = $this->getJson(route('admin.shows.show', [
            'show' => 'non-existent-id'
        ]));

        $response->assertNotFound();
    }

    /**
     * Show was successfully fetched
     */
    public function test_show_was_successfully_fetched()
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
        $show = Show::factory()
                    ->create();
        
        Sanctum::actingAs($admin, ['*']);
        $response = $this->getJson(route('admin.shows.show', [
            'show' => $show
        ]));

        $response->assertOk();
    }
}
