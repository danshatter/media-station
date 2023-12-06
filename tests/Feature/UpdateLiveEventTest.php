<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\{LiveEvent, Role, User};
use App\Exceptions\EventTimeTakenException;
use Tests\TestCase;

class UpdateLiveEventTest extends TestCase
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
        $response = $this->putJson(route('admin.live-events.update', [
            'liveEvent' => 'non-existent-id'
        ]));

        $response->assertNotFound();
    }

    /**
     * Validation errors
     *
     * @return void
     */
    public function test_validation_errors_occur_while_updating_live_event()
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
        $response = $this->putJson(route('admin.live-events.update', [
            'liveEvent' => $liveEvent
        ]), [
            'name' => null
        ]);

        $response->assertInvalid(['name']);
        $response->assertUnprocessable();
    }

    /**
     * Event time has already been scheduled by another event
     */
    public function test_event_time_has_already_been_scheduled_by_another_event()
    {
        $this->withoutExceptionHandling();
        $this->expectException(EventTimeTakenException::class);
        $eventName1 = 'The role of all things good';
        $eventName2 = 'Good things in having a lot of money';
        $liveEvent1 = LiveEvent::factory()
                            ->create([
                                'name' => $eventName1,
                                'starts_at' => now()->addDays(5)->startOfMinute()
                            ]);
        $liveEvent2 = LiveEvent::factory()
                            ->create([
                                'name' => $eventName2,
                                'starts_at' => now()->addDays(4)->startOfMinute()
                            ]);
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
        $response = $this->putJson(route('admin.live-events.update', [
            'liveEvent' => $liveEvent1
        ]), [
            'name' => $liveEvent1,
            'date' => $liveEvent2->starts_at->format('Y-m-d'),
            'time' => $liveEvent2->starts_at->format('H:i')
        ]);
    }

    /**
     * Live event was updated successfully
     */
    public function test_tag_was_successfully_updated()
    {
        $oldName = 'The effect of modern society';
        $newName = 'The role of the mother in the family';
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
                            ->create([
                                'name' => $oldName
                            ]);

        Sanctum::actingAs($admin, ['*']);
        $response = $this->putJson(route('admin.live-events.update', [
            'liveEvent' => $liveEvent
        ]), [
            'name' => $newName,
            'date' => now()->addDays(5)->format('Y-m-d'),
            'time' => now()->format('H:i')
        ]);
        $liveEvent->refresh();

        $response->assertOk();
        $response->assertValid();
        $this->assertSame($newName, $liveEvent->name);
    }
}
