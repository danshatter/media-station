<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Content, Show};

class TopVideosController extends Controller
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

        // Get the top videos
        $topVideos = Content::withRelationships($request->user())
                            ->with([
                                'contentable',
                                'tags'
                            ])
                            ->where('contentable_type', Show::class)
                            ->orderByDesc('views_count')
                            ->paginate($perPage);

        return $this->sendSuccess(__('app.request_successful'), 200, $topVideos);
    }
}
