<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{Role, User};
use Tests\TestCase;

class TopVideosTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Top videos were successfully fetched
     */
    public function test_top_videos_were_successfully_fetched()
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
                    
        $response = $this->getJson(route('top-videos'));

        $response->assertOk();
    }
}
