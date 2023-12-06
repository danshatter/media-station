<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\{Show, Role, User};
use Tests\TestCase;

class FollowShowTest extends TestCase
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
        $response = $this->postJson(route('shows.user-follow', [
            'show' => 'non-existent-id'
        ]));

        $response->assertNotFound();
    }

    /**
     * User followed show successfully
     */
    public function test_user_successfully_followed_show()
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
        $response = $this->postJson(route('shows.user-follow', [
            'show' => $show
        ]));

        $response->assertOk();
        $this->assertDatabaseHas('show_user', [
            'user_id' => $user->id,
            'show_id' => $show->id
        ]);
    }
}
