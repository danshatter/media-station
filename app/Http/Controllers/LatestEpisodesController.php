<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Content;

class LatestEpisodesController extends Controller
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

        // We get the latest contents of shows or podcasts that the user is following
        $latestEpisodes = Content::withRelationships($request->user())
                                ->with([
                                    'contentable',
                                    'tags'
                                ])
                                ->has('contentable')
                                ->latest()
                                ->paginate($perPage);

        return $this->sendSuccess(__('app.request_successful'), 200, $latestEpisodes);
    }
}
