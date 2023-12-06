<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Show;

class SuggestedShowsController extends Controller
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

        // Get the suggested shows based on number of followers
        $suggestedShows = Show::withRelationships($request->user())
                            ->with(['category'])
                            ->has('contents')
                            ->whereDoesntHave('users', fn($query) => $query->where('user_id', $request->user()->id))
                            ->orderByDesc('followers_count')
                            ->orderByDesc('total_views_count')
                            ->paginate($perPage);

        return $this->sendSuccess(__('app.request_successful'), 200, $suggestedShows);
    }
}
