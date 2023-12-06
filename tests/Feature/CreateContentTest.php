<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use App\Models\{Role, Show, Tag, User};
use Tests\TestCase;

class CreateContentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Validation errors
     *
     * @return void
     */
    public function test_validation_errors_occur_while_creating_content()
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
        $response = $this->postJson(route('admin.contents.store'), [
            'title' => null
        ]);

        $response->assertInvalid(['title']);
        $response->assertUnprocessable();
    }

    /**
     * Content was created successfully
     */
    public function test_content_was_successfully_created()
    {
        $image = UploadedFile::fake()->create('image.png', 1024);
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
        $tag = Tag::factory()
                ->create();

        Sanctum::actingAs($admin, ['*']);
        $response = $this->postJson(route('admin.contents.store'), [
            'show_or_podcast_id' => $show->id,
            'title' => fake()->sentence(),
            'description' => fake()->sentence(),
            'media_url' => fake()->url(),
            'author' => fake()->name(),
            'subtitle' => fake()->sentence(),
            'summary' => fake()->sentence(),
            'duration_in_minutes' => fake()->numberBetween(1, 300),
            'type' => 'video/mp4',
            'upload_as' => 'SHOW',
            'explicit' => fake()->word(),
            'season' => fake()->numberBetween(1, 9),
            'episode_type' => fake()->word(),
            'image' => $image,
            'tag_ids' => [$tag->id]
        ]);

        $response->assertValid();
        $response->assertCreated();
        $this->assertDatabaseHas('contents', [
            'contentable_id' => $show->id,
            'contentable_type' => Show::class
        ]);
        $this->assertDatabaseHas('content_tag', [
            'tag_id' => $tag->id
        ]);
        Storage::disk(config('filesystems.default'))->assertExists('images/'.$image->hashName());
    }
}
