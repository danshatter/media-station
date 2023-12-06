<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\{Content, Role, User};
use Tests\TestCase;

class ContentIsFavouriteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Content not found
     */
    public function test_content_is_not_found()
    {
        $userRole = Role::factory()
                        ->user()
                        ->create();
        $adminRole = Role::factory()
                        ->administrator()
                        ->create();
        $user = User::factory()
                    ->users()
                    ->create();
        
        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('contents.is-favourite', [
            'content' => 'non-existent-id'
        ]));

        $response->assertNotFound();
    }

    /**
     * Request was successful
     */
    public function test_request_was_successful_fetched()
    {
        $userRole = Role::factory()
                        ->user()
                        ->create();
        $adminRole = Role::factory()
                        ->administrator()
                        ->create();
        $user = User::factory()
                    ->users()
                    ->create();
        $content = Content::factory()
                        ->create();
        
        Sanctum::actingAs($user, ['*']);
        $response = $this->getJson(route('contents.is-favourite', [
            'content' => $content
        ]));

        $response->assertOk();
    }
}
