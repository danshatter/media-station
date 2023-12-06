<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\{Show, Role, User};
use Tests\TestCase;

class UnfollowShowTest extends TestCase
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
        $response = $this->postJson(route('shows.user-unfollow', [
            'show' => 'non-existent-id'
        ]));

        $response->assertNotFound();
    }

    /**
     * User unfollowed show successfully
     */
    public function test_user_successfully_unfollowed_show()
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
        $response = $this->postJson(route('shows.user-unfollow', [
            'show' => $show
        ]));

        $response->assertOk();
        $this->assertDatabaseMissing('show_user', [
            'user_id' => $user->id,
            'show_id' => $show->id
        ]);
    }
}
