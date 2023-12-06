<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{Role, User};
use Tests\TestCase;

class LatestVideosTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Latest videos were successfully fetched
     */
    public function test_latest_videos_were_successfully_fetched()
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

        $response = $this->getJson(route('latest-videos'));

        $response->assertOk();
    }
}
