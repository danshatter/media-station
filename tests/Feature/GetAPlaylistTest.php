<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\{Playlist, Role, User};
use Tests\TestCase;

class GetAPlaylistTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Playlist not found
     */
    public function test_playlist_is_not_found()
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
        $response = $this->getJson(route('playlists.show', [
            'playlistId' => 'non-existent-id'
        ]));

        $response->assertNotFound();
    }

    /**
     * Playlist was successfully fetched
     */
    public function test_playlist_was_successfully_fetched()
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
        $playlist = Playlist::factory()
                            ->for($user)
                            ->create();
        
        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('playlists.show', [
            'playlistId' => $playlist->id
        ]));

        $response->assertOk();
    }
}
