<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Exceptions\CustomException;
use App\Exceptions\EventTimeTakenException;
use App\Http\Requests\{StoreLiveEventRequest, UpdateLiveEventRequest};
use App\Models\LiveEvent;

class LiveEventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page') ?? config('fbn.per_page');

        $liveEvents = LiveEvent::latest()
                            ->paginate($perPage);

        return $this->sendSuccess(__('app.request_successful'), 200, $liveEvents);
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
     * @param  \App\Http\Requests\StoreLiveEventRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLiveEventRequest $request)
    {
        $data = $request->validated();

        // The timezone of the country
        $countryTimezone = config('fbn.country_timezone');

        // Parse the start time
        $startTime = Carbon::parse($data['date'].' '.$data['time']);

        if ($startTime < Carbon::parse(now()->timezone($countryTimezone)->addHour()->toDateTimeString())) {
            throw new CustomException('The live event start time must be at least '.now()->addHour()->diffForHumans().'.', 400);
        }

        // We check if a live there created on the same day
        if (LiveEvent::whereDate('starts_at', $startTime)
                    ->whereTime('starts_at', $startTime)
                    ->exists()) {
            throw new EventTimeTakenException;
        }

        // Create the live event
        $liveEvent = LiveEvent::create([
            'name' => $data['name'],
            'starts_at' => $startTime
        ]);

        return $this->sendSuccess('Live event created successfully.', 201, $liveEvent);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\LiveEvent  $liveEvent
     * @return \Illuminate\Http\Response
     */
    public function show(LiveEvent $liveEvent)
    {
        return $this->sendSuccess(__('app.request_successful'), 200, $liveEvent);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LiveEvent  $liveEvent
     * @return \Illuminate\Http\Response
     */
    public function edit(LiveEvent $liveEvent)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateLiveEventRequest  $request
     * @param  \App\Models\LiveEvent  $liveEvent
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLiveEventRequest $request, LiveEvent $liveEvent)
    {
        $data = $request->validated();

        // The timezone of the country
        $countryTimezone = config('fbn.country_timezone');

        // Parse the start time
        $startTime = Carbon::parse($data['date'].' '.$data['time']);

        if ($startTime < Carbon::parse(now()->timezone($countryTimezone)->addHour()->toDateTimeString())) {
            throw new CustomException('The live event start time must be at least '.now()->addHour()->diffForHumans().'.', 400);
        }

        // We check if a live there created on the same day
        if (LiveEvent::whereDate('starts_at', $startTime)
                    ->whereTime('starts_at', $startTime)
                    ->where('id', '!=', $liveEvent->id)
                    ->exists()) {
            throw new EventTimeTakenException;
        }

        $liveEvent->update([
            'name' => $data['name'],
            'starts_at' => $startTime
        ]);

        return $this->sendSuccess('Live event updated successfully.', 200, $liveEvent);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LiveEvent  $liveEvent
     * @return \Illuminate\Http\Response
     */
    public function destroy(LiveEvent $liveEvent)
    {
        $liveEvent->delete();

        return $this->sendSuccess('Live event deleted successfully.');
    }

    /**
     * Get the upcoming live events
     */
    public function upcoming(Request $request)
    {
        $perPage = $request->query('per_page') ?? config('fbn.per_page');

        // The timezone of the country
        $countryTimezone = config('fbn.country_timezone');

        $liveEvents = LiveEvent::where('starts_at', '>', now()->timezone($countryTimezone)->toDateTimeString())
                            ->orderBy('starts_at')
                            ->paginate($perPage);

        return $this->sendSuccess(__('app.request_successful'), 200, $liveEvents);
    }
}
