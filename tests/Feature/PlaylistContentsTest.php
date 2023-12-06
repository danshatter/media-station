<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\{Content, Playlist, Role, User};
use Tests\TestCase;

class PlaylistContentsTest extends TestCase
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
        $content = Content::factory()
                        ->create();
        
        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('playlists.contents', [
            'playlistId' => 'non-existent-id'
        ]));

        $response->assertNotFound();
    }

    /**
     * Playlist contents were successfully fetched
     */
    public function test_playlist_contents_were_successfully_fetched()
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
        $response = $this->getJson(route('playlists.contents', [
            'playlistId' => $playlist->id
        ]));

        $response->assertOk();
    }
}
