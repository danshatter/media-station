<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\{StoreQueueRequest, UpdateQueueRequest};
use App\Models\{Queue, Content};

class QueueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page') ?? config('fbn.per_page');

        $queue = Content::withRelationships($request->user())
                        ->with([
                            'contentable',
                            'tags'
                        ])
                        ->whereHas('queues', fn($query) => $query->where('user_id', $request->user()->id))
                        ->latest()
                        ->paginate($perPage);

        return $this->sendSuccess(__('app.request_successful'), 200, $queue);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreQueueRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreQueueRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Queue  $queue
     * @return \Illuminate\Http\Response
     */
    public function show(Queue $queue)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Queue  $queue
     * @return \Illuminate\Http\Response
     */
    public function edit(Queue $queue)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateQueueRequest  $request
     * @param  \App\Models\Queue  $queue
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateQueueRequest $request, Queue $queue)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Queue  $queue
     * @return \Illuminate\Http\Response
     */
    public function destroy(Queue $queue)
    {
        //
    }
}
