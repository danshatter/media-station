<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\{Playlist, Role, User};
use Tests\TestCase;

class UpdatePlaylistTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Validation errors
     *
     * @return void
     */
    public function test_validation_errors_occur_while_updating_playlist()
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
                            ->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->putJson(route('playlists.update', [
            'playlistId' => 'non-existent-id'
        ]), [
            'name' => null
        ]);

        $response->assertInvalid(['name']);
        $response->assertUnprocessable();
    }

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
        $response = $this->putJson(route('playlists.update', [
            'playlistId' => 'non-existent-id'
        ]), [
            'name' => 'soul'
        ]);

        $response->assertNotFound();
    }

    /**
     * Playlist was updated successfully
     */
    public function test_playlist_was_successfully_updated()
    {
        $oldName = 'soul';
        $newName = 'rock music';
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
                            ->create([
                                'name' => $oldName
                            ]);

        Sanctum::actingAs($user, ['*']);
        $response = $this->putJson(route('playlists.update', [
            'playlistId' => $playlist->id
        ]), [
            'name' => $newName
        ]);
        $playlist->refresh();

        $response->assertOk();
        $response->assertValid();
        $this->assertSame($newName, $playlist->name);
    }
}
