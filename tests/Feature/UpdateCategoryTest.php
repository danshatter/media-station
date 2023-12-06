<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use App\Models\{Category, Role, User};
use Tests\TestCase;

class UpdateCategoryTest extends TestCase
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
        $response = $this->postJson(route('admin.categories.update', [
            'category' => 'non-existent-id'
        ]));

        $response->assertNotFound();
    }

    /**
     * Validation errors
     *
     * @return void
     */
    public function test_validation_errors_occur_while_updating_category()
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
        $response = $this->postJson(route('admin.categories.update', [
            'category' => $category
        ]), [
            'name' => null
        ]);

        $response->assertInvalid(['name']);
        $response->assertUnprocessable();
    }

    /**
     * Category was updated successfully
     */
    public function test_category_was_successfully_updated()
    {
        $image = UploadedFile::fake()->create('image.png', 1024);
        $oldName = 'inspirational';
        $newName = 'educational';
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
                            ->create([
                                'name' => $oldName
                            ]);

        Sanctum::actingAs($admin, ['*']);
        $response = $this->postJson(route('admin.categories.update', [
            'category' => $category
        ]), [
            'name' => $newName,
            'image' => $image
        ]);
        $category->refresh();

        $response->assertOk();
        $response->assertValid();
        $this->assertSame($newName, $category->name);
        Storage::disk(config('filesystems.default'))->assertExists('categories/'.$image->hashName());
    }
}
