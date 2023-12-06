<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{Role, User};
use Tests\TestCase;

class PopularShowsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Popular shows were successfully fetched
     */
    public function test_popular_shows_were_successfully_fetched()
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

        $response = $this->getJson(route('popular-shows'));

        $response->assertOk();
    }
}
