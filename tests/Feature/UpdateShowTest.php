<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use App\Models\{Category, Show, Role, User};
use Tests\TestCase;

class UpdateShowTest extends TestCase
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
        
        Sanctum::actingAs($admin, ['*']);
        $response = $this->postJson(route('admin.shows.update', [
            'show' => 'non-existent-id'
        ]));

        $response->assertNotFound();
    }

    /**
     * Validation errors
     *
     * @return void
     */
    public function test_validation_errors_occur_while_updating_show()
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

        Sanctum::actingAs($admin, ['*']);
        $response = $this->postJson(route('admin.shows.update', [
            'show' => $show
        ]), [
            'name' => null
        ]);

        $response->assertInvalid(['name']);
        $response->assertUnprocessable();
    }

    /**
     * Show was updated successfully
     */
    public function test_show_was_successfully_updated()
    {
        $image = UploadedFile::fake()->create('image.png', 1024);
        $oldName = 'guyz zone';
        $newName = 'the place for guyz';
        $userRole = Role::factory()
                        ->user()
                        ->create();
        $adminRole = Role::factory()
                        ->administrator()
                        ->create();
        $admin = User::factory()
                    ->administrators()
                    ->create();
        $category1 = Category::factory()
                    ->create();
        $category2 = Category::factory()
                            ->create();          
        $show = Show::factory()
                    ->for($category1)
                    ->create([
                        'name' => $oldName
                    ]);

        Sanctum::actingAs($admin, ['*']);
        $response = $this->postJson(route('admin.shows.update', [
            'show' => $show
        ]), [
            'category_id' => $category2->id,
            'name' => $newName,
            'description' => fake()->sentence(),
            'link' => fake()->url(),
            'subtitle' => fake()->sentence(),
            'summary' => fake()->sentence(),
            'owner_name' => fake()->name(),
            'owner_email' => fake()->email(),
            'explicit' => fake()->word(),
            'type' => fake()->word(),
            'image' => $image
        ]);
        $show->refresh();

        $response->assertOk();
        $response->assertValid();
        $this->assertSame($newName, $show->name);
        $this->assertSame($category2->id, $show->category_id);
        Storage::disk(config('filesystems.default'))->assertExists('shows/'.$image->hashName());
    }
}
