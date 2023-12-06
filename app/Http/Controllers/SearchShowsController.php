<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SearchShowsRequest;
use App\Models\Show;

class SearchShowsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(SearchShowsRequest $request)
    {
        $perPage = $request->query('per_page') ?? config('fbn.per_page');

        $data = $request->validated();

        $shows = Show::withRelationships($request->user())
                    ->with(['category'])
                    ->where('name', 'LIKE', '%'.$data['name'].'%')
                    ->orderByDesc('followers_count')
                    ->paginate($perPage);

        return $this->sendSuccess(__('app.request_successful'), 200, $shows);
    }
}
