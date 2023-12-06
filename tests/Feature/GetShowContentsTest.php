<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\{Content, Show, Role, User};
use Tests\TestCase;

class GetShowContentsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Show not found
     */
    public function test_show_is_not_found()
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
        $content = Content::factory()
                        ->create();
        
        Sanctum::actingAs($admin, ['*']);
        $response = $this->getJson(route('admin.shows.contents', [
            'show' => 'non-existent-id'
        ]));

        $response->assertNotFound();
    }

    /**
     * Show contents fetched successfully
     */
    public function test_show_contents_were_successfully_fetched()
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
        $show = Show::factory()
                    ->create();
        $content = Content::factory()
                        ->for($show, 'contentable')
                        ->create();

        Sanctum::actingAs($admin, ['*']);
        $response = $this->getJson(route('admin.shows.contents', [
            'show' => $show
        ]));

        $response->assertOk();
    }
}
