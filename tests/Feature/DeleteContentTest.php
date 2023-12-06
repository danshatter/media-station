<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\{Content, Role, User};
use Tests\TestCase;

class DeleteContentTest extends TestCase
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
        $admin = User::factory()
                    ->administrators()
                    ->create();
        
        Sanctum::actingAs($admin, ['*']);
        $response = $this->deleteJson(route('admin.contents.show', [
            'content' => 'non-existent-id'
        ]));

        $response->assertNotFound();
    }

    /**
     * Content was successfully deleted
     */
    public function test_content_was_successfully_deleted()
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
        $response = $this->deleteJson(route('admin.contents.destroy', [
            'content' => $content
        ]));

        $response->assertOk();
        $this->assertModelMissing($content);
    }
}
