<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{Role, User};
use Tests\TestCase;

class LatestEpisodesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Latest episodes were successfully fetched
     */
    public function test_latest_episodes_were_successfully_fetched()
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

        $response = $this->getJson(route('latest-episodes'));

        $response->assertOk();
    }
}
