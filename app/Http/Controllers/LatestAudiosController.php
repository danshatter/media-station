<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Content, Podcast};

class LatestAudiosController extends Controller
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

        // We get the latest audios
        $latestAudios = Content::withRelationships($request->user())
                            ->with([
                                'contentable',
                                'tags'
                            ])
                            ->where('contentable_type', Podcast::class)
                            ->latest()
                            ->paginate($perPage);

        return $this->sendSuccess(__('app.request_successful'), 200, $latestAudios);
    }
}
