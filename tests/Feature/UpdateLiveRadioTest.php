<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\{Live, Role, User};
use Tests\TestCase;

class UpdateLiveRadioTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Validation errors
     *
     * @return void
     */
    public function test_validation_errors_occur_while_updating_live_radio()
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
        $response = $this->postJson(route('admin.live-radio.store'), [
            'link' => null
        ]);

        $response->assertInvalid(['link']);
        $response->assertUnprocessable();
    }

    /**
     * Live radio was created successfully
     */
    public function test_live_radio_was_successfully_created()
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
        $url = fake()->url();

        Sanctum::actingAs($admin, ['*']);
        $response = $this->postJson(route('admin.live-radio.store'), [
            'link' => $url
        ]);

        $response->assertValid();
        $response->assertOk();
        $this->assertDatabaseHas('lives', [
            'type' => Live::RADIO,
            'link' => $url
        ]);
    }
}
