<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{Role, User};
use Tests\TestCase;

class TopAudiosTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Top audios were successfully fetched
     */
    public function test_top_audios_were_successfully_fetched()
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

        $response = $this->getJson(route('top-audios'));

        $response->assertOk();
    }
}
