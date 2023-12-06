<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\LiveEvent;
use Tests\TestCase;

class GetALiveEventTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Live event not found
     */
    public function test_live_event_is_not_found()
    {
        $response = $this->getJson(route('live-events.show', [
            'liveEvent' => 'non-existent-id'
        ]));

        $response->assertNotFound();
    }

    /**
     * Live event was successfully fetched
     */
    public function test_event_was_successfully_fetched()
    {
        $liveEvent = LiveEvent::factory()
                            ->create();
        
        $response = $this->getJson(route('live-events.show', [
            'liveEvent' => $liveEvent
        ]));

        $response->assertOk();
    }
}
