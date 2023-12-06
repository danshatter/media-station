<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\{Content, Playlist, Role, User};
use Tests\TestCase;

class RemoveFromPlaylistTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Validation errors
     *
     * @return void
     */
    public function test_validation_errors_occur_while_removing_from_playlist()
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
        $response = $this->postJson(route('playlists.remove', [
            'playlistId' => 'non-existent-id'
        ]), [
            'content_id' => null
        ]);

        $response->assertInvalid(['content_id']);
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
        $content = Content::factory()
                        ->create();
        
        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('playlists.remove', [
            'playlistId' => 'non-existent-id'
        ]), [
            'content_id' => $content->id
        ]);

        $response->assertNotFound();
    }

    /**
     * Content removed from playlist successfully
     */
    public function test_content_was_successfully_removed_from_playlist()
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
        $content = Content::factory()
                        ->create();
        $playlist->contents()
                ->toggle($content);

        Sanctum::actingAs($user, ['*']);
        $response = $this->postJson(route('playlists.remove', [
            'playlistId' => $playlist->id
        ]), [
            'content_id' => $content->id
        ]);

        $response->assertOk();
        $response->assertValid();
        $this->assertDatabaseMissing('content_playlist', [
            'playlist_id' => $playlist->id,
            'content_id' => $content->id
        ]);
    }
}
