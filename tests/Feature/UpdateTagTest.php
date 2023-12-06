<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\{Tag, Role, User};
use Tests\TestCase;

class UpdateTagTest extends TestCase
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
        $response = $this->putJson(route('admin.tags.update', [
            'tag' => 'non-existent-id'
        ]));

        $response->assertNotFound();
    }

    /**
     * Validation errors
     *
     * @return void
     */
    public function test_validation_errors_occur_while_updating_tag()
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
        $response = $this->putJson(route('admin.tags.update', [
            'tag' => $tag
        ]), [
            'name' => null
        ]);

        $response->assertInvalid(['name']);
        $response->assertUnprocessable();
    }

    /**
     * Tag was updated successfully
     */
    public function test_tag_was_successfully_updated()
    {
        $oldName = 'shopping';
        $newName = 'reality';
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
                    ->create([
                        'name' => $oldName
                    ]);

        Sanctum::actingAs($admin, ['*']);
        $response = $this->putJson(route('admin.tags.update', [
            'tag' => $tag
        ]), [
            'name' => $newName
        ]);
        $tag->refresh();

        $response->assertOk();
        $response->assertValid();
        $this->assertSame($newName, $tag->name);
    }
}
