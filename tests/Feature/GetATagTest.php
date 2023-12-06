<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\{Role, Tag, User};
use Tests\TestCase;

class GetATagTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Tag not found
     */
    public function test_tag_is_not_found()
    {
        $userRole = Role::factory()
                        ->user()
                        ->create();
        $adminRole = Role::factory()
                        ->administrator()
                        ->create();
        $admin = User::factory()
                    ->administrators()
                    ->create();
        
        Sanctum::actingAs($admin, ['*']);
        $response = $this->getJson(route('admin.tags.show', [
            'tag' => 'non-existent-id'
        ]));

        $response->assertNotFound();
    }

    /**
     * Tag was successfully fetched
     */
    public function test_tag_was_successfully_fetched()
    {
        $userRole = Role::factory()
                        ->user()
                        ->create();
        $adminRole = Role::factory()
                        ->administrator()
                        ->create();
        $admin = User::factory()
                    ->administrators()
                    ->create();
        $tag = Tag::factory()
                ->create();
        
        Sanctum::actingAs($admin, ['*']);
        $response = $this->getJson(route('admin.tags.show', [
            'tag' => $tag
        ]));

        $response->assertOk();
    }
}
