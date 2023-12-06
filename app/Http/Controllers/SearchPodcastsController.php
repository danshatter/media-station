<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SearchPodcastsRequest;
use App\Models\Podcast;

class SearchPodcastsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(SearchPodcastsRequest $request)
    {
        $perPage = $request->query('per_page') ?? config('fbn.per_page');

        $data = $request->validated();

        $podcasts = Podcast::withRelationships($request->user())
                        ->with(['category'])
                        ->where('name', 'LIKE', '%'.$data['name'].'%')
                        ->orderByDesc('followers_count')
                        ->paginate($perPage);

        return $this->sendSuccess(__('app.request_successful'), 200, $podcasts);
    }
}
