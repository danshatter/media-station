<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\{Category, Role, User};
use Tests\TestCase;

class DeleteCategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Category not found
     */
    public function test_category_is_not_found()
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
        $response = $this->deleteJson(route('admin.categories.destroy', [
            'category' => 'non-existent-id'
        ]));

        $response->assertNotFound();
    }

    /**
     * Category was successfully deleted
     */
    public function test_category_was_successfully_deleted()
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
        $category = Category::factory()
                            ->create();
        
        Sanctum::actingAs($admin, ['*']);
        $response = $this->deleteJson(route('admin.categories.destroy', [
            'category' => $category
        ]));

        $response->assertOk();
        $this->assertModelMissing($category);
    }
}
