<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Podcast;

class FeaturedPodcastsController extends Controller
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

        // Get the featured podcasts of a user
        $podcasts = Podcast::withRelationships($request->user())
                        ->with(['category'])
                        ->withCount(['views' => fn($query) => $query->where('views.user_id', $request->user()->id)])
                        ->whereHas('users', fn($query) => $query->where('users.id', $request->user()->id))
                        ->orderByDesc('views_count')
                        ->paginate($perPage);

        return $this->sendSuccess(__('app.request_successful'), 200, $podcasts);
    }
}
