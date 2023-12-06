<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Advert;

class GetAUserAdvertTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Advert not found
     */
    public function test_advert_was_not_found()
    {
        $response = $this->getJson(route('adverts.user-show', [
            'advert' => 'non-existent-id'
        ]));

        $response->assertNotFound();
    }

    /**
     * Advert were successfully fetched
     */
    public function test_adverts_were_successfully_fetched()
    {
        $advert = Advert::factory()
                        ->create();

        $response = $this->getJson(route('adverts.user-show', [
            'advert' => $advert
        ]));

        $response->assertOk();
    }
}
