<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Show;

class RecommendedShowsController extends Controller
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

        // Get the recommended shows based on number of followers
        $recommendedShows = Show::withRelationships($request->user())
                                ->withCount(['views' => fn($query) => $query->where('views.user_id', $request->user()->id)])
                                ->with(['category'])
                                ->has('contents')
                                ->whereDoesntHave('users', fn($query) => $query->where('user_id', $request->user()->id))
                                ->orderByDesc('views_count')
                                ->orderByDesc('followers_count')
                                ->orderByDesc('total_views_count')
                                ->paginate($perPage);

        return $this->sendSuccess(__('app.request_successful'), 200, $recommendedShows);
    }
}
