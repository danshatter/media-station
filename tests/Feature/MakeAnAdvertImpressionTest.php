<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Advert;
use Tests\TestCase;

class MakeAnAdvertImpressionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Advert not found
     */
    public function test_advert_was_not_found()
    {
        $response = $this->postJson(route('adverts.impressions', [
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

        $response = $this->postJson(route('adverts.impressions', [
            'advert' => $advert
        ]));
        $advert->refresh();

        $response->assertOk();
        $this->assertSame(1, $advert->impressions);
    }
}
