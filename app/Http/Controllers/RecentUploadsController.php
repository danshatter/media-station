<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Content;

class RecentUploadsController extends Controller
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

        // Get the recently uploaded contents
        $recentUploads = Content::withRelationships($request->user())
                                ->with([
                                    'contentable',
                                    'tags'
                                ])
                                ->latest()
                                ->paginate($perPage);

        return $this->sendSuccess(__('app.request_successful'), 200, $recentUploads);
    }
}
