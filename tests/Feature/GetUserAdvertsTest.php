<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetUserAdvertsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Adverts were successfully fetched
     */
    public function test_adverts_were_successfully_fetched()
    {
        $response = $this->getJson(route('adverts.user-index'));

        $response->assertOk();
    }
}
