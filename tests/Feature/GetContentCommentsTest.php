<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Content;
use Tests\TestCase;

class GetContentCommentsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Content not found
     */
    public function test_content_is_not_found()
    {
        $response = $this->getJson(route('contents.index-comments', [
            'content' => 'non-existent-id'
        ]));

        $response->assertNotFound();
    }

    /**
     * Content comments were successfully fetched
     */
    public function test_content_comments_were_successfully_fetched()
    {
        $content = Content::factory()
                        ->create();

        $response = $this->getJson(route('contents.index-comments', [
            'content' => $content
        ]));

        $response->assertOk();
    }
}
