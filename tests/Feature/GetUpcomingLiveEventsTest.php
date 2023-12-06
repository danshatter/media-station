<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetUpcomingLiveEventsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Live events were successfully fetched
     */
    public function test_live_events_were_successfully_fetched()
    {
        $response = $this->getJson(route('live-events.upcoming'));

        $response->assertOk();
    }
}
