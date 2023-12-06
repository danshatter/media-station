<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SearchContentsRequest;
use App\Models\Content;

class SearchContentsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(SearchContentsRequest $request)
    {
        $perPage = $request->query('per_page') ?? config('fbn.per_page');

        $data = $request->validated();

        // Get the contents by search query
        $contents = Content::withRelationships($request->user())
                        ->with([
                            'contentable',
                            'tags'
                        ])
                        ->when($data['title'] ?? null, fn($query, $value) => $query->where('title', 'LIKE', "%{$value}%"))
                        ->when($data['text'] ?? null, fn($query, $value) => $query->where('summary', 'LIKE', "%{$value}%")
                                                                                ->orWhere('subtitle', 'LIKE', "%{$value}%")
                                                                                ->orWhere('description', 'LIKE', "%{$value}%"))
                        ->orderByDesc('views_count')
                        ->paginate($perPage);

        return $this->sendSuccess(__('app.request_successful'), 200, $contents);
    }
}
