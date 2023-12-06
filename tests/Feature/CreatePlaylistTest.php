<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\{Role, User};
use Tests\TestCase;

class CreatePlaylistTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Validation errors
     *
     * @return void
     */
    public function test_validation_errors_occur_while_creating_playlist()
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

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('playlists.store'), [
            'name' => null
        ]);

        $response->assertInvalid(['name']);
        $response->assertUnprocessable();
    }

    /**
     * Playlist was created successfully
     */
    public function test_playlist_was_successfully_created()
    {
        $name = 'soul';
        $userRole = Role::factory()
                        ->user()
                        ->create();
        $adminRole = Role::factory()
                        ->administrator()
                        ->create();
        $user = User::factory()
                    ->users()
                    ->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('playlists.store'), [
            'name' => $name
        ]);

        $response->assertValid();
        $response->assertCreated();
        $this->assertDatabaseHas('playlists', [
            'name' => $name,
        ]);
    }
}
