<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{Role, User};
use Tests\TestCase;

class RecentUploadsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Recent uploads were successfully fetched
     */
    public function test_recent_uploads_were_successfully_fetched()
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

        $response = $this->getJson(route('recent-uploads'));

        $response->assertOk();
    }
}
