<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\{Show, Role, User};
use Tests\TestCase;

class GetAUserShowTest extends TestCase
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
        $response = $this->getJson(route('shows.user-show', [
            'show' => 'non-existent-id'
        ]));

        $response->assertNotFound();
    }

    /**
     * User show were successfully fetched
     */
    public function test_user_show_were_successfully_fetched()
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
        $user->shows()
            ->toggle($show);

        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('shows.user-show', [
            'show' => $show
        ]));

        $response->assertOk();
    }
}
