<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\{Show, Role, User};
use Tests\TestCase;

class DeleteShowTest extends TestCase
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
        $response = $this->deleteJson(route('admin.shows.show', [
            'show' => 'non-existent-id'
        ]));

        $response->assertNotFound();
    }

    /**
     * Show was successfully deleted
     */
    public function test_show_was_successfully_deleted()
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
        $response = $this->deleteJson(route('admin.shows.destroy', [
            'show' => $show
        ]));

        $response->assertOk();
        $this->assertModelMissing($show);
    }
}
