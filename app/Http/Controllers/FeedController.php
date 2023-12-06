<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Http, DB};
use Illuminate\Support\Carbon;
use App\Http\Requests\UploadFeedRequest;
use App\Exceptions\CustomException;
use App\Models\{Category, Tag};
use App\Services\ThirdParty\Spreaker as SpreakerService;

class FeedController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(UploadFeedRequest $request)
    {
        $data = $request->validated();

        $response = Http::get($data['url']);

        if ($response->failed()) {
            throw new CustomException('Unable to process feed.', 503);
        }

        // Process the data from the RSS feed
        $feedData = app()->make(SpreakerService::class, ['feed' => $response->body()])->processFeed();

        // The contents data
        $contentsData = $feedData['episodes'];

        $keywords = collect($contentsData)->pluck('itunes.keywords')
                                                ->filter(fn($item) => !empty($item))
                                                ->reduce(fn($acc, $item) => $acc->merge($item), collect([]))
                                                ->unique()
                                                ->values()
                                                ->toArray();

        // Create all the data from the RSS feed
        DB::transaction(function() use ($feedData, $keywords) {
            // The podcast data
            $podcastData = $feedData['podcast'];

            // The contents data
            $contentsData = $feedData['episodes'];

            // Create the keywords as tags
            foreach ($keywords as $keyword) {
                Tag::firstOrCreate([
                    'name' => $keyword
                ]);
            }

            // Create the category
            $category = Category::firstOrCreate([
                'name' => $podcastData['category']
            ]);

            // Create the podcast
            $podcast = $category->podcasts()->updateOrCreate([
                'name' => $podcastData['title'],
                'description' => $podcastData['description'],
                'link' => $podcastData['link'],
                'image_url' => $podcastData['image'],
                'owner' => $podcastData['itunes']['owner'],
                'subtitle' => $podcastData['itunes']['subtitle'],
                'summary' => $podcastData['itunes']['summary'],
                'explicit' => $podcastData['itunes']['explicit'],
                'type' => $podcastData['itunes']['type']
            ]);

            // Create the contents
            foreach ($contentsData as $key => $contentData) {
                $content = $podcast->contents()->updateOrCreate([
                    'title' => $contentData['name']
                ], [
                    'description' => $contentData['description'],
                    'guid' => $contentData['guid'],
                    'published_at' => isset($contentData['published_at']) ? Carbon::parse($contentData['published_at']) : null,
                    'enclosure_url' => $contentData['enclosure_url'],
                    'type' => $contentData['type'],
                    'author' => $contentData['itunes']['author'],
                    'subtitle' => $contentData['itunes']['subtitle'],
                    'summary' => $contentData['itunes']['summary'],
                    'duration' => $contentData['itunes']['duration'],
                    'explicit' => $contentData['itunes']['explicit'],
                    'season' => $contentData['itunes']['season'],
                    'episode_type' => $contentData['itunes']['episode_type'],
                    'image_url' => $contentData['itunes']['image'],
                ]);

                // Add the tags of the content
                $content->tags()
                        ->sync(Tag::whereIn('name', $contentData['itunes']['keywords'] ?? [])->get());
            }
        });

        return $this->sendSuccess('Feed uploaded successfully.');
    }
}
