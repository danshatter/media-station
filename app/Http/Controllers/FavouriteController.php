<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\{StoreFavouriteRequest, UpdateFavouriteRequest};
use App\Models\{Favourite, Content};

class FavouriteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page') ?? config('fbn.per_page');

        $favourites = Content::withRelationships($request->user())
                            ->with([
                                'contentable',
                                'tags'
                            ])
                            ->whereHas('favourites', fn($query) => $query->where('user_id', $request->user()->id))
                            ->latest()
                            ->paginate($perPage);

        return $this->sendSuccess(__('app.request_successful'), 200, $favourites);
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
     * @param  \App\Http\Requests\StoreFavouriteRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreFavouriteRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Favourite  $favourite
     * @return \Illuminate\Http\Response
     */
    public function show(Favourite $favourite)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Favourite  $favourite
     * @return \Illuminate\Http\Response
     */
    public function edit(Favourite $favourite)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateFavouriteRequest  $request
     * @param  \App\Models\Favourite  $favourite
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFavouriteRequest $request, Favourite $favourite)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Favourite  $favourite
     * @return \Illuminate\Http\Response
     */
    public function destroy(Favourite $favourite)
    {
        //
    }
}
