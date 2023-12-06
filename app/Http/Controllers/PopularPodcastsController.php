<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Podcast;

class PopularPodcastsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $perPage = $request->query('per_page') ?? config('fbn.per_page');

        // Get the popular podcasts based on number of followers
        $popularPodcasts = Podcast::withRelationships($request->user())
                                ->with(['category'])
                                ->orderByDesc('followers_count')
                                ->orderByDesc('total_views_count')
                                ->paginate($perPage);

        return $this->sendSuccess(__('app.request_successful'), 200, $popularPodcasts);
    }
}
