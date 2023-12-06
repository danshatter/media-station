<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Category;
use Tests\TestCase;

class GetACategoryPodcastsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Category not found
     */
    public function test_category_is_not_found()
    {        
        $response = $this->getJson(route('categories.show-podcasts', [
            'category' => 'non-existent-id'
        ]));

        $response->assertNotFound();
    }

    /**
     * Category was successfully fetched
     */
    public function test_category_was_successfully_fetched()
    {
        $category = Category::factory()
                            ->create();
        
        $response = $this->getJson(route('categories.show-podcasts', [
            'category' => $category
        ]));

        $response->assertOk();
    }
}
