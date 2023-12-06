<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Content, Podcast};

class TopAudiosController extends Controller
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

        // Get the top audios
        $topAudios = Content::withRelationships($request->user())
                            ->with([
                                'contentable',
                                'tags'
                            ])
                            ->where('contentable_type', Podcast::class)
                            ->orderByDesc('views_count')
                            ->paginate($perPage);

        return $this->sendSuccess(__('app.request_successful'), 200, $topAudios);
    }
}
