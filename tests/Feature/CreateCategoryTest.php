<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use App\Models\{Role, User};
use Tests\TestCase;

class CreateCategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Validation errors
     *
     * @return void
     */
    public function test_validation_errors_occur_while_creating_category()
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
        $response = $this->postJson(route('admin.categories.store'), [
            'name' => null
        ]);

        $response->assertInvalid(['name']);
        $response->assertUnprocessable();
    }

    /**
     * Category was created successfully
     */
    public function test_category_was_successfully_created()
    {
        $image = UploadedFile::fake()->create('image.png', 1024);
        $name = 'inspirational';
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
        $response = $this->postJson(route('admin.categories.store'), [
            'name' => $name,
            'image' => $image
        ]);

        $response->assertValid();
        $response->assertCreated();
        $this->assertDatabaseHas('categories', [
            'name' => $name,
        ]);
        Storage::disk(config('filesystems.default'))->assertExists('categories/'.$image->hashName());
    }
}
