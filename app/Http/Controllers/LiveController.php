<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLiveRequest;
use App\Http\Requests\UpdateLiveRadioRequest;
use App\Http\Requests\UpdateLiveRequest;
use App\Models\Live;
use Illuminate\Http\Request;

class LiveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Http\Requests\StoreLiveRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLiveRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Live  $live
     * @return \Illuminate\Http\Response
     */
    public function show(Live $live)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Live  $live
     * @return \Illuminate\Http\Response
     */
    public function edit(Live $live)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateLiveRequest  $request
     * @param  \App\Models\Live  $live
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLiveRequest $request, Live $live)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Live  $live
     * @return \Illuminate\Http\Response
     */
    public function destroy(Live $live)
    {
        //
    }

    /**
     * Get the live radio details
     */
    public function indexRadio(Request $request)
    {
        $liveRadio = Live::firstWhere('type', Live::RADIO);

        return $this->sendSuccess(__('app.request_successful'), 200, $liveRadio);
    }

    /**
     * Update the live radio details
     */
    public function storeRadio(UpdateLiveRadioRequest $request)
    {
        $data = $request->validated();

        // Update the live radio details
        $liveRadio = Live::updateOrCreate([
            'type' => Live::RADIO
        ], [
            'link' => $data['link']
        ]);

        return $this->sendSuccess('Live radio details updated successfully.', 200, $liveRadio);
    }
}
