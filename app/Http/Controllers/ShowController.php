<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\{StoreShowRequest, UpdateShowRequest};
use App\Models\{Category, Show, Content};
use App\Exceptions\CustomException;

class ShowController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page') ?? config('fbn.per_page');

        $shows = Show::withAdminRelationships()
                    ->with(['category'])
                    ->latest()
                    ->paginate($perPage);

        return $this->sendSuccess(__('app.request_successful'), 200, $shows);
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
     * @param  \App\Http\Requests\StoreShowRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreShowRequest $request)
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
            $image = $data['image']->store('shows', $fileDriver);

            // Get the image url
            $imageUrl = Storage::disk($fileDriver)
                            ->url($image);
        }

        // Get the category of the show
        $category = Category::find($data['category_id']);

        $show = $category->shows()->create([
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

        $show->loadAdminRelationships();

        return $this->sendSuccess('Show created successfully.', 201, $show->load(['category']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Show  $show
     * @return \Illuminate\Http\Response
     */
    public function show(Show $show)
    {
        $show->loadAdminRelationships();
        $show->load(['category']);

        return $this->sendSuccess(__('app.request_successful'), 200, $show);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Show  $show
     * @return \Illuminate\Http\Response
     */
    public function edit(Show $show)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateShowRequest  $request
     * @param  \App\Models\Show  $show
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateShowRequest $request, Show $show)
    {
        $data = $request->validated();

        // Initialize the image details
        $fileDriver = $show->file_driver;
        $image = $show->image;
        $imageUrl = $show->image_url;

        // If there is an image uploaded we store the image
        if ($request->hasFile('image')) {
            // The disk to use for storage
            $fileDriver = config('filesystems.default');

            // Store the image
            $image = $data['image']->store('shows', $fileDriver);

            // Get the image url
            $imageUrl = Storage::disk($fileDriver)
                            ->url($image);

            // Check if there is an old image, then we delete the image
            if (isset($show->image)) {
                Storage::disk($show->file_driver)
                    ->delete($show->image);
            }
        }

        $category = Category::find($data['category_id']);

        // Update the show
        $show->forceFill([
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

        $show->loadAdminRelationships();

        return $this->sendSuccess('Show updated successfully.', 200, $show->load(['category']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Show  $show
     * @return \Illuminate\Http\Response
     */
    public function destroy(Show $show)
    {
        $show->delete();

        // Check if there is an old image, then we delete the image
        if (isset($show->image)) {
            Storage::disk($show->file_driver)
                ->delete($show->image);
        }

        return $this->sendSuccess('Show deleted successfully.');
    }

    /**
     * Get the contents of a show
     */
    public function contents(Request $request, Show $show)
    {
        $perPage = $request->query('per_page') ?? config('fbn.per_page');

        $contents = Content::withAdminRelationships()
                        ->with([
                            'contentable',
                            'tags'
                        ])
                        ->whereHas('contentable', fn($query) => $query->where('contentable_id', $show->id)
                                                                    ->where('contentable_type', Show::class))
                        ->latest()
                        ->paginate($perPage);

        return $this->sendSuccess(__('app.request_successful'), 200, $contents);
    }

    /**
     * Get the shows from a user
     */
    public function userIndex(Request $request)
    {
        $perPage = $request->query('per_page') ?? config('fbn.per_page');

        $shows = Show::withRelationships($request->user())
                    ->with(['category'])
                    ->latest()
                    ->paginate($perPage);

        return $this->sendSuccess(__('app.request_successful'), 200, $shows);
    }

    /**
     * Get a show from a user
     */
    public function userShow(Request $request, Show $show)
    {
        $show->loadRelationships($request->user());

        return $this->sendSuccess(__('app.request_successful'), 200, $show);
    }

    /**
     * Follow a show
     */
    public function userFollow(Request $request, Show $show)
    {
        // We check if the user has followed the show
        if ($request->user()
                    ->shows()
                    ->where('show_id', $show->id)
                    ->exists()) {
            throw new CustomException('You have already followed this show.', 400);
        }

        // Follow the show
        $request->user()
                ->shows()
                ->toggle($show);

        $show->loadRelationships($request->user());

        return $this->sendSuccess('Show followed successfully.');
    }

    /**
     * Unfollow a show
     */
    public function userUnfollow(Request $request, Show $show)
    {
        // We check if the user has is not following the show
        if ($request->user()
                    ->shows()
                    ->where('show_id', $show->id)
                    ->doesntExist()) {
            throw new CustomException('You are not following this show.', 400);
        }

        // Unfollow the show
        $request->user()
                ->shows()
                ->toggle($show);

        $show->loadRelationships($request->user());

        return $this->sendSuccess('Show unfollowed successfully.');
    }

    /**
     * Get the contents of a show
     */
    public function userContents(Request $request, Show $show)
    {
        $perPage = $request->query('per_page') ?? config('fbn.per_page');

        $showContents = Content::withRelationships($request->user())
                            ->with([
                                'contentable',
                                'tags'
                            ])
                            ->whereHas('contentable', fn($query) => $query->where('contentable_id', $show->id)
                                                                        ->where('contentable_type', Show::class))
                            ->latest()
                            ->paginate($perPage);

        return $this->sendSuccess(__('app.request_successful'), 200, $showContents);
    }

    /**
     * Check if a user is following a show
     */
    public function isFollowed(Request $request, Show $show)
    {
        $isFollowed = $show->users()
                        ->where('user_id', $request->user()->id)
                        ->exists();

        return $this->sendSuccess(__('app.request_successful'), 200, [
            'is_followed' => $isFollowed
        ]);
    }

    /**
     * Get the shows followed by a user
     */
    public function following(Request $request)
    {
        $perPage = $request->query('per_page') ?? config('fbn.per_page');

        $followedShows = $request->user()
                                ->shows()
                                ->withRelationships($request->user())
                                ->paginate($perPage);

        return $this->sendSuccess(__('app.request_successful'), 200, $followedShows);
    }
}
