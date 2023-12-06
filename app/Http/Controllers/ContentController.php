<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\{Storage, DB};
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\{StoreContentCommentRequest, StoreContentRequest, UpdateContentCommentRequest, UpdateContentRequest};
use App\Exceptions\{CustomException, ForbiddenException};
use App\Models\{Content, Comment, Podcast, Show};
use App\Services\Application as ApplicationService;

class ContentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page') ?? config('fbn.per_page');

        $contents = Content::withAdminRelationships()
                        ->with([
                            'contentable',
                            'tags'
                        ])
                        ->latest()
                        ->paginate($perPage);

        return $this->sendSuccess(__('app.request_successful'), 200, $contents);
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
     * @param  \App\Http\Requests\StoreContentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreContentRequest $request)
    {
        $data = $request->validated();

        // Initialize the the file driver, image and the image url
        $fileDriver = null;
        $image = null;
        $imageUrl = null;

        // Get the contentable
        switch ($data['upload_as']) {
            case 'PODCAST':
                $contentable = Podcast::find($data['show_or_podcast_id']);
            break;

            case 'SHOW':
                $contentable = Show::find($data['show_or_podcast_id']);
            break;
            
            default:
                throw new CustomException('An unknown error occurred. Please try again later.', 503);
            break;
        }

        // If there is an image uploaded we store the image
        if ($request->hasFile('image')) {
            // The disk to use for storage
            $fileDriver = config('filesystems.default');

            // Store the image
            $image = $data['image']->store('images', $fileDriver);

            // Get the image url
            $imageUrl = Storage::disk($fileDriver)
                            ->url($image);
        }

        /**
         * We check if the request has tags so we put it in a transaction else we just store the content
         */
        if ($request->has('tag_ids')) {
            $content = DB::transaction(function() use ($data, $image, $imageUrl, $fileDriver, $contentable) {
                $content = $contentable->contents()
                                    ->create([
                                        'title' => $data['title'],
                                        'description' => $data['description'],
                                        'published_at' => now(),
                                        'enclosure_url' => $data['media_url'],
                                        'type' => $data['type'],
                                        'author' => $data['author'],
                                        'subtitle' => $data['subtitle'],
                                        'summary' => $data['summary'],
                                        'duration' => $data['duration_in_minutes'].' '.Str::plural('minute', $data['duration_in_minutes']),
                                        'explicit' => $data['explicit'] ?? null,
                                        'season' => $data['season'] ?? null,
                                        'episode_type' => $data['episode_type'] ?? null,
                                        'image' => $image,
                                        'image_url' => $imageUrl,
                                        'file_driver' => $fileDriver
                                    ]);

                // Create the tags
                $content->tags()->sync($data['tag_ids']);

                return $content;
            });
        } else {
            // Store the content
            $content = $contentable->contents()
                                ->create([
                                    'title' => $data['title'],
                                    'description' => $data['description'],
                                    'published_at' => now(),
                                    'enclosure_url' => $data['media_url'],
                                    'type' => $data['type'],
                                    'author' => $data['author'],
                                    'subtitle' => $data['subtitle'],
                                    'summary' => $data['summary'],
                                    'duration' => $data['duration_in_minutes'].' '.Str::plural('minute', $data['duration_in_minutes']),
                                    'explicit' => $data['explicit'] ?? null,
                                    'season' => $data['season'] ?? null,
                                    'episode_type' => $data['episode_type'] ?? null,
                                    'image' => $image,
                                    'image_url' => $imageUrl,
                                    'file_driver' => $fileDriver
                                ]);
        }

        return $this->sendSuccess('Content created successfully.', 201, $content->loadAdminRelationships()
                                                                                ->load([
                                                                                    'contentable',
                                                                                    'tags'
                                                                                ]));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Content  $content
     * @return \Illuminate\Http\Response
     */
    public function show(Content $content)
    {
        $content->loadAdminRelationships();
        $content->load([
                    'contentable',
                    'tags'
                ]);

        return $this->sendSuccess(__('app.request_successful'), 200, $content);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Content  $content
     * @return \Illuminate\Http\Response
     */
    public function edit(Content $content)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateContentRequest  $request
     * @param  \App\Models\Content  $content
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateContentRequest $request, Content $content)
    {
        $data = $request->validated();

        // Initialize the image details
        $fileDriver = $content->file_driver;
        $image = $content->image;
        $imageUrl = $content->image_url;

        // Get the contentable
        switch ($data['upload_as']) {
            case 'PODCAST':
                $contentable = Podcast::find($data['show_or_podcast_id']);
            break;

            case 'SHOW':
                $contentable = Show::find($data['show_or_podcast_id']);
            break;
            
            default:
                throw new CustomException('An unknown error occurred. Please try again later.', 503);
            break;
        }

        // If there is an image uploaded we store the image
        if ($request->hasFile('image')) {
            // The disk to use for storage
            $fileDriver = config('filesystems.default');

            // Store the image
            $image = $data['image']->store('images', $fileDriver);

            // Get the image url
            $imageUrl = Storage::disk($fileDriver)
                            ->url($image);

            // Check if there is an old image, then we delete the image
            if (isset($content->image)) {
                Storage::disk($content->file_driver)
                    ->delete($content->image);
            }
        }

        $content = DB::transaction(function() use ($data, $image, $imageUrl, $fileDriver, $content) {
            // Update the content
            $content->forceFill([
                'title' => $data['title'],
                'description' => $data['description'],
                'published_at' => now(),
                'enclosure_url' => $data['media_url'],
                'type' => $data['type'],
                'author' => $data['author'],
                'subtitle' => $data['subtitle'],
                'summary' => $data['summary'],
                'duration' => $data['duration_in_minutes'].' '.Str::plural('minute', $data['duration_in_minutes']),
                'explicit' => $data['explicit'],
                'season' => $data['season'],
                'explicit' => $data['explicit'],
                'episode_type' => $data['episode_type'],
                'image' => $image,
                'image_url' => $imageUrl,
                'file_driver' => $fileDriver
            ])->save();

            // Update the tags
            $content->tags()->sync($data['tag_ids'] ?? []);

            return $content;
        });

        return $this->sendSuccess('Content updated successfully.', 200, $content->loadAdminRelationships()
                                                                                ->load([
                                                                                    'contentable',
                                                                                    'tags'
                                                                                ]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Content  $content
     * @return \Illuminate\Http\Response
     */
    public function destroy(Content $content)
    {
        $content->delete();

        // Check if there is an old image, then we delete the image
        if (isset($content->image)) {
            Storage::disk($content->file_driver)
                ->delete($content->image);
        }

        return $this->sendSuccess('Content deleted successfully.');
    }

    /**
     * Like a content
     */
    public function like(Request $request, Content $content)
    {
        // Check if the user has already liked the content
        if ($request->user()
                    ->likes()
                    ->where('content_id', $content->id)
                    ->exists()) {
            throw new CustomException('Content has already been liked.', 400);
        }

        // Like the post
        $request->user()
                ->likes()
                ->toggle($content);

        // Load the necessary content relationships
        $content->loadRelationships($request->user());

        return $this->sendSuccess('Content liked successfully.');
    }

    /**
     * Dislike a content
     */
    public function dislike(Request $request, Content $content)
    {
        // Check if the user has not been liked
        if ($request->user()
                    ->likes()
                    ->where('content_id', $content->id)
                    ->doesntExist()) {
            throw new CustomException('Content has not been liked.', 400);
        }

        // Dislike the post
        $request->user()
                ->likes()
                ->toggle($content);
        
        // Load the necessary content relationships
        $content->loadRelationships($request->user());

        return $this->sendSuccess('Content disliked successfully.');
    }

    /**
     * Add a content to the queue
     */
    public function queue(Request $request, Content $content)
    {
        // Check if the item has been added to the queue
        if ($request->user()
                    ->queues()
                    ->where('content_id', $content->id)
                    ->exists()) {
            throw new CustomException('Content already added to queue.', 400);
        }

        // Add item to queue
        $request->user()
                ->queues()
                ->updateOrCreate([
                    'content_id' => $content->id
                ]);

        // Load the necessary content relationships
        $content->loadRelationships($request->user());

        return $this->sendSuccess('Content added to queue successfully.');
    }

    /**
     * Remove a content from the queue
     */
    public function unqueue(Request $request, Content $content)
    {
        // Check if the item does not exist in the queue
        if ($request->user()
                    ->queues()
                    ->where('content_id', $content->id)
                    ->doesntExist()) {
            throw new CustomException('Content has not been queued.', 400);
        }

        // Remove item from queue
        $request->user()
                ->queues()
                ->where('content_id', $content->id)
                ->delete();

        // Load the necessary content relationships
        $content->loadRelationships($request->user());

        return $this->sendSuccess('Content removed from queue successfully.');
    }

    /**
     * Register the viewing of a content by a user
     */
    public function viewed(Request $request, Content $content)
    {
        // Register that the content has been viewed by the user
        $request->user()
                ->views()
                ->attach($content);

        // Load the necessary content relationships
        $content->loadRelationships($request->user());

        return $this->sendSuccess('Content successfully logged as viewed.');
    }

    /**
     * The comments on a content
     */
    public function indexComments(Request $request, Content $content)
    {
        $perPage = $request->query('per_page') ?? config('fbn.per_page');

        $comments = $content->comments()
                            ->with(['user'])
                            ->paginate($perPage);

        return $this->sendSuccess(__('app.request_successful'), 200, $comments);
    }

    /**
     * Create a comment on a content
     */
    public function storeComment(StoreContentCommentRequest $request, Content $content)
    {
        $data = $request->validated();

        // Create the comment
        $comment = $content->comments()
                        ->create([
                            'user_id' => $request->user()->id,
                            'body' => $data['body']
                        ]);

        return $this->sendSuccess('Comment created successfully.', 201, $comment);
    }

    /**
     * Get a comment on a content
     */
    public function showComment(Content $content, Comment $comment)
    {
        $comment->load(['user']);

        return $this->sendSuccess(__('app.request_successful'), 200, $comment);
    }

    /**
     * Update a comment on a content
     */
    public function updateComment(UpdateContentCommentRequest $request, Content $content, Comment $comment)
    {
        $data = $request->validated();

        /**
         * Check if the user is the comment creator
         */
        if ($comment->user_id !== $request->user()->id) {
            throw new ForbiddenException;
        }

        $comment->update($data);

        return $this->sendSuccess('Comment updated successfully.', 200, $comment);
    }

    /**
     * Delete a comment on a content
     */
    public function destroyComment(Request $request, Content $content, Comment $comment)
    {
        /**
         * Check if the user is the comment creator
         */
        if ($comment->user_id !== $request->user()->id) {
            throw new ForbiddenException;
        }

        $comment->delete();

        return $this->sendSuccess('Comment deleted successfully.');
    }

    /**
     * Add a content to favourites
     */
    public function favourite(Request $request, Content $content)
    {
        // Check if the content has been added to favourites
        if ($request->user()
                    ->favourites()
                    ->where('content_id', $content->id)
                    ->exists()) {
            throw new CustomException('Content already added to favourites.', 400);
        }

        // Add content to favourites
        $request->user()
                ->favourites()
                ->updateOrCreate([
                    'content_id' => $content->id
                ]);

        // Load the necessary content relationships
        $content->loadRelationships($request->user());

        return $this->sendSuccess('Content added to favourites successfully.');
    }

    /**
     * Remove a content from favourites
     */
    public function unfavourite(Request $request, Content $content)
    {
        // Check if the content has been removed from favourites
        if ($request->user()
                    ->favourites()
                    ->where('content_id', $content->id)
                    ->doesntExist()) {
            throw new CustomException('Content is not added to favourites.', 400);
        }

        // Remove content from favourites
        $request->user()
                ->favourites()
                ->where('content_id', $content->id)
                ->delete();

        // Load the necessary content relationships
        $content->loadRelationships($request->user());

        return $this->sendSuccess('Content removed from favourites successfully.');
    }

    /**
     * Check if a content has been liked by a user
     */
    public function isLiked(Request $request, Content $content)
    {
        $isLiked = $content->likes()
                        ->where('user_id', $request->user()->id)
                        ->exists();

        return $this->sendSuccess(__('app.request_successful'), 200, [
            'is_liked' => $isLiked
        ]);
    }

    /**
     * Check if a content has been queued by a user
     */
    public function isQueued(Request $request, Content $content)
    {
        $isQueued = $content->queues()
                            ->where('user_id', $request->user()->id)
                            ->exists();

        return $this->sendSuccess(__('app.request_successful'), 200, [
            'is_queued' => $isQueued
        ]);
    }

    /**
     * Check if a content has been added to favourites by a user
     */
    public function isFavourite(Request $request, Content $content)
    {
        $isFavourite = $content->favourites()
                            ->where('user_id', $request->user()->id)
                            ->exists();

        return $this->sendSuccess(__('app.request_successful'), 200, [
            'is_favourite' => $isFavourite
        ]);
    }

    /**
     * Check if a content has been viewed by a user
     */
    public function isViewed(Request $request, Content $content)
    {
        $isViewed = $content->views()
                            ->where('user_id', $request->user()->id)
                            ->exists();

        return $this->sendSuccess(__('app.request_successful'), 200, [
            'is_viewed' => $isViewed
        ]);
    }
}
