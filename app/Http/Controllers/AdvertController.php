<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\{StoreAdvertRequest, UpdateAdvertRequest};
use App\Models\Advert;
use App\Http\Resources\AdvertAdminResource;

class AdvertController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $adverts = Advert::all();

        return $this->sendSuccess(__('app.request_successful'), 200, AdvertAdminResource::collection($adverts));
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
     * @param  \App\Http\Requests\StoreAdvertRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAdvertRequest $request)
    {
        $data = $request->validated();

        // Initialize the the file driver, image and the image url
        $fileDriver = null;
        $image = null;
        $imageUrl = null;

        // If there is an image uploaded we store the image
        if ($request->hasFile('image')) {
            // The disk to use for storage
            $fileDriver = config('filesystems.default');

            // Store the image
            $image = $data['image']->store('adverts', $fileDriver);

            // Get the image url
            $imageUrl = Storage::disk($fileDriver)
                            ->url($image);
        }

        // Store the advert
        $advert = Advert::create([
            'image' => $image,
            'image_url' => $imageUrl,
            'file_driver' => $fileDriver,
            'url' => $data['url'],
            'position' => $data['position']
        ]);

        return $this->sendSuccess('Advert created successfully.', 201, new AdvertAdminResource($advert));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Advert  $advert
     * @return \Illuminate\Http\Response
     */
    public function show(Advert $advert)
    {
        return $this->sendSuccess(__('app.request_successful'), 200, new AdvertAdminResource($advert));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Advert  $advert
     * @return \Illuminate\Http\Response
     */
    public function edit(Advert $advert)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAdvertRequest  $request
     * @param  \App\Models\Advert  $advert
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAdvertRequest $request, Advert $advert)
    {
        $data = $request->validated();

        // Initialize the image details
        $fileDriver = $advert->file_driver;
        $image = $advert->image;
        $imageUrl = $advert->image_url;

        // If there is an image uploaded we store the image
        if ($request->hasFile('image')) {
            // The disk to use for storage
            $fileDriver = config('filesystems.default');

            // Store the image
            $image = $data['image']->store('adverts', $fileDriver);

            // Get the image url
            $imageUrl = Storage::disk($fileDriver)
                            ->url($image);

            // Check if there is an old image, then we delete the image
            if (isset($advert->image) && isset($advert->file_driver)) {
                Storage::disk($advert->file_driver)
                    ->delete($advert->image);
            }
        }

        // Update the advert
        $advert->update([
            'image' => $image,
            'image_url' => $imageUrl,
            'file_driver' => $fileDriver,
            'url' => $data['url'],
            'position' => $data['position']
        ]);

        return $this->sendSuccess('Advert updated successfully', 200, new AdvertAdminResource($advert));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Advert  $advert
     * @return \Illuminate\Http\Response
     */
    public function destroy(Advert $advert)
    {
        $advert->delete();

        // Check if there is an old image, then we delete the image
        if (isset($advert->image) && isset($advert->file_driver)) {
            Storage::disk($advert->file_driver)
                ->delete($advert->image);
        }

        return $this->sendSuccess('Advert deleted successfully.');
    }

    /**
     * Get the impressions for a user
     */
    public function userIndex(Request $request)
    {
        $adverts = Advert::all();

        return $this->sendSuccess(__('app.request_successful'), 200, $adverts);
    }

    /**
     * Get an impression for a user
     */
    public function userShow(Request $request, Advert $advert)
    {
        return $this->sendSuccess(__('app.request_successful'), 200, $advert);
    }

    /**
     * Increase the count of the advert impressions
     */
    public function impressions(Advert $advert)
    {
        // Increase the counter of the impressions
        $advert->increment('impressions');

        return $this->sendSuccess('Impression increased sucessfully.');
    }
}
