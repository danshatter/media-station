<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\{LiveEvent, Role, User};
use App\Exceptions\EventTimeTakenException;
use Tests\TestCase;

class CreateLiveEventTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Validation errors
     *
     * @return void
     */
    public function test_validation_errors_occur_while_creating_live_event()
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
        $response = $this->postJson(route('admin.live-events.store'), [
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
        $liveEvent = LiveEvent::factory()
                            ->create([
                                'name' => $eventName1,
                                'starts_at' => now()->addDays(5)->startOfMinute()
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
        $response = $this->postJson(route('admin.live-events.store'), [
            'name' => $eventName2,
            'date' => $liveEvent->starts_at->format('Y-m-d'),
            'time' => $liveEvent->starts_at->format('H:i')
        ]);
    }

    /**
     * Live event was created successfully
     */
    public function test_live_event_was_successfully_created()
    {
        $name = fake()->sentence();
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
        $response = $this->postJson(route('admin.live-events.store'), [
            'name' => $name,
            'date' => now()->addDays(5)->format('Y-m-d'),
            'time' => now()->format('H:i')
        ]);

        $response->assertValid();
        $response->assertCreated();
        $this->assertDatabaseHas('live_events', [
            'name' => $name,
        ]);
    }
}
