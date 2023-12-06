<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{View, Content};

class RecentlyPlayedController extends Controller
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

        // Get the recently played contents by a user
        $recentlyPlayed = Content::withRelationships($request->user())
                                ->with([
                                    'contentable',
                                    'tags'
                                ])
                                ->whereHas('views', fn($query) => $query->where('user_id', $request->user()->id))
                                ->orderByDesc(View::select(['created_at'])
                                                ->whereColumn('content_id', 'contents.id')
                                                ->latest()
                                                ->take(1))
                                ->paginate($perPage);

        return $this->sendSuccess(__('app.request_successful'), 200, $recentlyPlayed);
    }
}
