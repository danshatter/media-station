<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Http\Requests\{StoreCategoryRequest, UpdateCategoryRequest};
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page') ?? config('fbn.per_page');

        $categories = Category::latest()
                            ->paginate($perPage);

        return $this->sendSuccess(__('app.request_successful'), 200, $categories);
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
     * @param  \App\Http\Requests\StoreCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCategoryRequest $request)
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
            $image = $data['image']->store('categories', $fileDriver);

            // Get the image url
            $imageUrl = Storage::disk($fileDriver)
                            ->url($image);
        }

        $category = Category::create([
            'name' => $data['name'],
            'image' => $image,
            'image_url' => $imageUrl,
            'file_driver' => $fileDriver
        ]);

        return $this->sendSuccess('Category created successfully.', 201, $category);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return $this->sendSuccess(__('app.request_successful'), 200, $category);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCategoryRequest  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $data = $request->validated();

        // Initialize the image details
        $fileDriver = $category->file_driver;
        $image = $category->image;
        $imageUrl = $category->image_url;

        // If there is an image uploaded we store the image
        if ($request->hasFile('image')) {
            // The disk to use for storage
            $fileDriver = config('filesystems.default');

            // Store the image
            $image = $data['image']->store('categories', $fileDriver);

            // Get the image url
            $imageUrl = Storage::disk($fileDriver)
                            ->url($image);

            // Check if there is an old image, then we delete the image
            if (isset($category->image) && isset($category->file_driver)) {
                Storage::disk($category->file_driver)
                    ->delete($category->image);
            }
        }

        $category->update([
            'name' => $data['name'],
            'image' => $image,
            'image_url' => $imageUrl,
            'file_driver' => $fileDriver
        ]);

        return $this->sendSuccess('Category updated successfully.', 200, $category);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category->delete();

        // Check if there is an old image, then we delete the image
        if (isset($category->image) && isset($category->file_driver)) {
            Storage::disk($category->file_driver)
                ->delete($category->image);
        }

        return $this->sendSuccess('Category deleted successfully.');
    }

    /**
     * Get the podcasts of a category
     */
    public function showPodcasts(Request $request, Category $category)
    {
        $perPage = $request->query('per_page') ?? config('fbn.per_page');

        $podcasts = $category->podcasts()
                            ->latest()
                            ->paginate($perPage);

        return $this->sendSuccess(__('app.request_successful'), 200, $podcasts);
    }

    /**
     * Get the shows of a category
     */
    public function showShows(Request $request, Category $category)
    {
        $perPage = $request->query('per_page') ?? config('fbn.per_page');

        $shows = $category->shows()
                        ->latest()
                        ->paginate($perPage);

        return $this->sendSuccess(__('app.request_successful'), 200, $shows);
    }
}
