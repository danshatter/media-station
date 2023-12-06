<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\{Playlist, Role, User};
use Tests\TestCase;

class DeletePlaylistTest extends TestCase
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
        $user = User::factory()
                    ->users()
                    ->create();
        
        Sanctum::actingAs($user, ['*']);
        $response = $this->deleteJson(route('playlists.destroy', [
            'playlistId' => 'non-existent-id'
        ]));

        $response->assertNotFound();
    }

    /**
     * Playlist was successfully deleted
     */
    public function test_playlist_was_successfully_deleted()
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
        $response = $this->deleteJson(route('playlists.destroy', [
            'playlistId' => $playlist->id
        ]));

        $response->assertOk();
        $this->assertModelMissing($playlist);
    }
}
