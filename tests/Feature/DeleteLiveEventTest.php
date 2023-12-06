<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\{LiveEvent, Role, User};
use Tests\TestCase;

class DeleteLiveEventTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Live event not found
     */
    public function test_live_event_is_not_found()
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
        $response = $this->deleteJson(route('admin.live-events.destroy', [
            'liveEvent' => 'non-existent-id'
        ]));

        $response->assertNotFound();
    }

    /**
     * Live event was successfully deleted
     */
    public function test_live_event_was_successfully_deleted()
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
        $liveEvent = LiveEvent::factory()
                            ->create();
        
        Sanctum::actingAs($admin, ['*']);
        $response = $this->deleteJson(route('admin.live-events.destroy', [
            'liveEvent' => $liveEvent
        ]));

        $response->assertOk();
        $this->assertModelMissing($liveEvent);
    }
}
