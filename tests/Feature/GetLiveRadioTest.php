<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetLiveRadioTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Live radio was successfully fetched
     */
    public function test_live_radio_was_successfully_fetched()
    {
        $response = $this->getJson(route('live-radio.index'));

        $response->assertOk();
    }
}
