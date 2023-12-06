<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\DiscoverRequest;
use App\Models\Content;

class DiscoverController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(DiscoverRequest $request)
    {
        $perPage = $request->query('per_page') ?? config('fbn.per_page');

        $data = $request->validated();

        $discoveries = Content::withRelationships($request->user())
                            ->with([
                                'contentable',
                                'tags'
                            ])
                            ->whereHas('tags', fn($query) => $query->where('name', $data['tag']))
                            ->orderByDesc('views_count')
                            ->paginate($perPage);

        return $this->sendSuccess(__('app.request_successful'), 200, $discoveries);
    }
}
