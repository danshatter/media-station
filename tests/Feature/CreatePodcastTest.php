<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use App\Models\{Category, Role, User};
use Tests\TestCase;

class CreatePodcastTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Validation errors
     *
     * @return void
     */
    public function test_validation_errors_occur_while_creating_podcast()
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
        $response = $this->postJson(route('admin.podcasts.store'), [
            'name' => null
        ]);

        $response->assertInvalid(['name']);
        $response->assertUnprocessable();
    }

    /**
     * Podcast was created successfully
     */
    public function test_podcast_was_successfully_created()
    {
        $image = UploadedFile::fake()->create('image.png', 1024);
        $name = 'ladies lounge with Ademorayo';
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
        $response = $this->postJson(route('admin.podcasts.store'), [
            'category_id' => $category->id,
            'name' => $name,
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

        $response->assertValid();
        $response->assertCreated();
        $this->assertDatabaseHas('podcasts', [
            'name' => $name,
        ]);
        Storage::disk(config('filesystems.default'))->assertExists('podcasts/'.$image->hashName());
    }
}
