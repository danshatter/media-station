<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\{StorePodcastRequest, UpdatePodcastRequest};
use App\Models\{Category, Podcast, Content};
use App\Exceptions\CustomException;

class PodcastController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page') ?? config('fbn.per_page');

        $podcasts = Podcast::withAdminRelationships()
                        ->with(['category'])
                        ->latest()
                        ->paginate($perPage);

        return $this->sendSuccess(__('app.request_successful'), 200, $podcasts);
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
     * @param  \App\Http\Requests\StorePodcastRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePodcastRequest $request)
    {
        $data = $request->validated();

        // Initialize the the file driver, image and the image url
        $fileDriver = null;
        $image = null;
        $imageUrl = null;

        // If there is an image uploaded we store the image
        if ($request->hasFile('image')) {
            // The disk to use for storage
            $fileDriver = config('filesystems.default');

            // Store the image
            $image = $data['image']->store('podcasts', $fileDriver);

            // Get the image url
            $imageUrl = Storage::disk($fileDriver)
                            ->url($image);
        }

        // Get the category of the podcast
        $category = Category::find($data['category_id']);

        $podcast = $category->podcasts()->create([
            'name' => $data['name'],
            'description' => $data['description'],
            'link' => $data['link'],
            'subtitle' => $data['subtitle'],
            'summary' => $data['summary'],
            'owner' => [
                'name' => $data['owner_name'],
                'email' => $data['email'] ?? null
            ],
            'explicit' => $data['explicit'] ?? null,
            'type' => $data['type'] ?? null,
            'image' => $image,
            'image_url' => $imageUrl,
            'file_driver' => $fileDriver
        ]);

        $podcast->loadAdminRelationships();

        return $this->sendSuccess('Podcast created successfully.', 201, $podcast->load(['category']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Podcast  $podcast
     * @return \Illuminate\Http\Response
     */
    public function show(Podcast $podcast)
    {
        $podcast->loadAdminRelationships();
        $podcast->load(['category']);

        return $this->sendSuccess(__('app.request_successful'), 200, $podcast);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Podcast  $podcast
     * @return \Illuminate\Http\Response
     */
    public function edit(Podcast $podcast)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePodcastRequest  $request
     * @param  \App\Models\Podcast  $podcast
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePodcastRequest $request, Podcast $podcast)
    {
        $data = $request->validated();

        // Initialize the image details
        $fileDriver = $podcast->file_driver;
        $image = $podcast->image;
        $imageUrl = $podcast->image_url;

        // If there is an image uploaded we store the image
        if ($request->hasFile('image')) {
            // The disk to use for storage
            $fileDriver = config('filesystems.default');

            // Store the image
            $image = $data['image']->store('podcasts', $fileDriver);

            // Get the image url
            $imageUrl = Storage::disk($fileDriver)
                            ->url($image);

            // Check if there is an old image, then we delete the image
            if (isset($podcast->image)) {
                Storage::disk($podcast->file_driver)
                    ->delete($podcast->image);
            }
        }

        $category = Category::find($data['category_id']);

        // Update the podcast
        $podcast->forceFill([
            'category_id' => $category->id,
            'name' => $data['name'],
            'description' => $data['description'],
            'link' => $data['link'],
            'subtitle' => $data['subtitle'],
            'summary' => $data['summary'],
            'owner' => [
                'name' => $data['owner_name'],
                'email' => $data['email'] ?? null
            ],
            'explicit' => $data['explicit'] ?? null,
            'type' => $data['type'] ?? null,
            'image' => $image,
            'image_url' => $imageUrl,
            'file_driver' => $fileDriver
        ])->save();

        $podcast->loadAdminRelationships();

        return $this->sendSuccess('Podcast updated successfully.', 200, $podcast->load(['category']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Podcast  $podcast
     * @return \Illuminate\Http\Response
     */
    public function destroy(Podcast $podcast)
    {
        $podcast->delete();

        // Check if there is an old image, then we delete the image
        if (isset($podcast->image)) {
            Storage::disk($podcast->file_driver)
                ->delete($podcast->image);
        }

        return $this->sendSuccess('Podcast deleted successfully.');
    }

    /**
     * Get the contents of a podcast
     */
    public function contents(Request $request, Podcast $podcast)
    {
        $perPage = $request->query('per_page') ?? config('fbn.per_page');

        $contents = Content::withAdminRelationships()
                        ->with([
                            'contentable',
                            'tags'
                        ])
                        ->whereHas('contentable', fn($query) => $query->where('contentable_id', $podcast->id)
                                                                    ->where('contentable_type', Podcast::class))
                        ->latest()
                        ->paginate($perPage);

        return $this->sendSuccess(__('app.request_successful'), 200, $contents);
    }

    /**
     * Get the podcasts from a user
     */
    public function userIndex(Request $request)
    {
        $perPage = $request->query('per_page') ?? config('fbn.per_page');

        $podcasts = Podcast::withRelationships($request->user())
                        ->with(['category'])
                        ->withCount(['contents'])
                        ->latest()
                        ->paginate($perPage);

        return $this->sendSuccess(__('app.request_successful'), 200, $podcasts);
    }

    /**
     * Get a podcast from a user
     */
    public function userShow(Request $request, Podcast $podcast)
    {
        $podcast->loadRelationships($request->user());
        $podcast->load(['category']);
        $podcast->loadCount(['contents']);

        return $this->sendSuccess(__('app.request_successful'), 200, $podcast);
    }

    /**
     * Follow a podcast
     */
    public function userFollow(Request $request, Podcast $podcast)
    {
        // We check if the user has followed the podcast
        if ($request->user()
                    ->podcasts()
                    ->where('podcast_id', $podcast->id)
                    ->exists()) {
            throw new CustomException('You have already followed this podcast.', 400);
        }

        // Follow the podcast
        $request->user()
                ->podcasts()
                ->toggle($podcast);
        
        $podcast->loadRelationships($request->user());

        return $this->sendSuccess('Podcast followed successfully.');
    }

    /**
     * Unfollow a podcast
     */
    public function userUnfollow(Request $request, Podcast $podcast)
    {
        // We check if the user has is not following the podcast
        if ($request->user()
                    ->podcasts()
                    ->where('podcast_id', $podcast->id)
                    ->doesntExist()) {
            throw new CustomException('You are not following this podcast.', 400);
        }

        // Unfollow the podcast
        $request->user()
                ->podcasts()
                ->toggle($podcast);

        $podcast->loadRelationships($request->user());

        return $this->sendSuccess('Podcast unfollowed successfully.');
    }

    /**
     * Get the contents of a podcast
     */
    public function userContents(Request $request, Podcast $podcast)
    {
        $perPage = $request->query('per_page') ?? config('fbn.per_page');

        $podcastContents = Content::withRelationships($request->user())
                                ->with([
                                    'contentable',
                                    'tags'
                                ])
                                ->whereHas('contentable', fn($query) => $query->where('contentable_id', $podcast->id)
                                                                            ->where('contentable_type', Podcast::class))
                                ->latest()
                                ->paginate($perPage);

        return $this->sendSuccess(__('app.request_successful'), 200, $podcastContents);
    }

    /**
     * Check if a user is following a podcast
     */
    public function isFollowed(Request $request, Podcast $podcast)
    {
        $isFollowed = $podcast->users()
                            ->where('user_id', $request->user()->id)
                            ->exists();

        return $this->sendSuccess(__('app.request_successful'), 200, [
            'is_followed' => $isFollowed
        ]);
    }

    /**
     * Get the podcasts followed by a user
     */
    public function following(Request $request)
    {
        $perPage = $request->query('per_page') ?? config('fbn.per_page');

        $followedPodcasts = $request->user()
                                    ->podcasts()
                                    ->withRelationships($request->user())
                                    ->paginate($perPage);

        return $this->sendSuccess(__('app.request_successful'), 200, $followedPodcasts);
    }
}
