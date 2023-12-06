<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\{Role, User};
use Tests\TestCase;

class RecentlyPlayedTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Recently played were successfully fetched
     */
    public function test_recently_played_were_successfully_fetched()
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

        Sanctum::actingAs($users, ['*']);
        $response = $this->getJson(route('recently-played'));

        $response->assertOk();
    }
}
