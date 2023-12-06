<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\{Show, Role, User};
use Tests\TestCase;

class GetAUserShowContentsTest extends TestCase
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
        $response = $this->getJson(route('shows.user-contents', [
            'show' => 'non-existent-id'
        ]));

        $response->assertNotFound();
    }

    /**
     * Show contents fetched successfully
     */
    public function test_user_show_contents_were_successfully_fetched()
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
        $response = $this->getJson(route('shows.user-contents', [
            'show' => $show
        ]));

        $response->assertOk();
    }
}
