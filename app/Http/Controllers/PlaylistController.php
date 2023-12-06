<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\{AddToPlaylistRequest, RemoveFromPlaylistRequest, StorePlaylistRequest, UpdatePlaylistRequest};
use App\Models\{Playlist, Content};
use App\Exceptions\CustomException;

class PlaylistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page') ?? config('fbn.per_page');

        $playlists = $request->user()
                            ->playlists()
                            ->latest()
                            ->paginate($perPage);

        return $this->sendSuccess(__('app.request_successful'), 200, $playlists);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePlaylistRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePlaylistRequest $request)
    {
        $data = $request->validated();

        // Create the playlist
        $playlist = $request->user()
                            ->playlists()
                            ->create($data);

        return $this->sendSuccess('Playlist created successfully.', 201, $playlist);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Playlist  $playlist
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $playlistId)
    {
        $playlist = $request->user()
                            ->playlists()
                            ->findOrFail($playlistId);

        return $this->sendSuccess(__('app.request_successful'), 200, $playlist);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Playlist  $playlist
     * @return \Illuminate\Http\Response
     */
    public function edit(Playlist $playlist)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePlaylistRequest  $request
     * @param  \App\Models\Playlist  $playlist
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePlaylistRequest $request, $playlistId)
    {
        $data = $request->validated();

        $playlist = $request->user()
                            ->playlists()
                            ->findOrFail($playlistId);

        // Update the playlist
        $playlist->update($data);

        return $this->sendSuccess('Playlist updated successfully.', 200, $playlist);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Playlist  $playlist
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $playlistId)
    {
        $playlist = $request->user()
                            ->playlists()
                            ->findOrFail($playlistId);

        $playlist->delete();

        return $this->sendSuccess('Playlist deleted successfully.');
    }

    /**
     * Add a content to a playlist
     */
    public function add(AddToPlaylistRequest $request, $playlistId)
    {
        $data = $request->validated();

        $playlist = $request->user()
                            ->playlists()
                            ->findOrFail($playlistId);

        // Check if the content has already been added to the playlist
        if ($playlist->contents()
                    ->where('content_id', $data['content_id'])
                    ->exists()) {
            throw new CustomException('Content already added to playlist.', 400);
        }

        $playlist->contents()
                ->toggle($data['content_id']);

        return $this->sendSuccess('Content added to playlist successfully.');
    }

    /**
     * Remove a content to a playlist
     */
    public function remove(RemoveFromPlaylistRequest $request, $playlistId)
    {
        $data = $request->validated();

        $playlist = $request->user()
                            ->playlists()
                            ->findOrFail($playlistId);

        // Check if the content does not exist in playlist
        if ($playlist->contents()
                    ->where('content_id', $data['content_id'])
                    ->doesntExist()) {
            throw new CustomException('Content has not been added to playlist.', 400);
        }

        $playlist->contents()
                ->toggle($data['content_id']);

        return $this->sendSuccess('Content removed from playlist successfully.');
    }

    /**
     * Get the contents of a playlist
     */
    public function contents(Request $request, $playlistId)
    {
        $perPage = $request->query('per_page') ?? config('fbn.per_page');

        $playlist = $request->user()
                            ->playlists()
                            ->findOrFail($playlistId);

        $playlistContents = Content::withRelationships($request->user())
                                ->with([
                                    'contentable',
                                    'tags'
                                ])
                                ->whereHas('playlists', fn($query) => $query->where('playlists.id', $playlist->id))
                                ->latest()
                                ->paginate($perPage);

        return $this->sendSuccess(__('app.request_successful'), 200, $playlistContents);
    }
}
