<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{Role, User};
use Tests\TestCase;

class DiscoverTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Validation errors
     *
     * @return void
     */
    public function test_validation_errors_occur_while_fetching_discoveries()
    {
        $userRole = Role::factory()
                        ->user()
                        ->create();
        $adminRole = Role::factory()
                        ->administrator()
                        ->create();
        $user = User::factory()
                    ->users()
                    ->create();

        $response = $this->getJson(route('discover', [
            'tag' => null
        ]));

        $response->assertInvalid(['tag']);
        $response->assertUnprocessable();
    }

    /**
     * Discoveries were successfully fetched
     */
    public function test_discoveries_were_successfully_fetched()
    {
        $userRole = Role::factory()
                    ->user()
                    ->create();
        $adminRole = Role::factory()
                        ->administrator()
                        ->create();
        $users = User::factory()
                    ->users()
                    ->create();

        $response = $this->getJson(route('discover', [
            'tag' => fake()->word()
        ]));

        $response->assertOk();
    }
}
