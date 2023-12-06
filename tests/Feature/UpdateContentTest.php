<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use App\Models\{Content, Role, Show, Tag, User};
use Tests\TestCase;

class UpdateContentTest extends TestCase
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
        $response = $this->postJson(route('admin.contents.update', [
            'content' => 'non-existent-id'
        ]));

        $response->assertNotFound();
    }

    /**
     * Validation errors
     *
     * @return void
     */
    public function test_validation_errors_occur_while_updating_content()
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
        $response = $this->postJson(route('admin.contents.update', [
            'content' => $content
        ]), [
            'title' => null
        ]);

        $response->assertInvalid(['title']);
        $response->assertUnprocessable();
    }

    /**
     * Content was updated successfully
     */
    public function test_content_was_successfully_updated()
    {
        $oldTitle = 'Talking about love';
        $newTitle = 'Uploading files with PHP';
        $image = UploadedFile::fake()->create('image.png');
        $userRole = Role::factory()
                        ->user()
                        ->create();
        $adminRole = Role::factory()
                        ->administrator()
                        ->create();
        $admin = User::factory()
                    ->administrators()
                    ->create();
        $show1 = Show::factory()
                    ->create();
        $show2 = Show::factory()
                    ->create();
        $tag1 = Tag::factory()
                ->create([
                    'name' => 'shopping'
                ]);
        $tag2 = Tag::factory()
                ->create([
                    'name' => 'fashion'
                ]);
        $content = Content::factory()
                        ->for($show1, 'contentable')
                        ->create([
                            'title' => $oldTitle
                        ]);
        $content->tags()->sync($tag1);

        Sanctum::actingAs($admin, ['*']);
        $response = $this->postJson(route('admin.contents.update', [
            'content' => $content
        ]), [
            'show_or_podcast_id' => $show2->id,
            'title' => $newTitle,
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
            'tag_ids' => [$tag2->id]
        ]);
        $content->refresh();

        $response->assertOk();
        $response->assertValid();
        // $this->assertSame($newTitle, $content->title);
        // $this->assertSame($show2->id, $content->contentable_id);
        $this->assertDatabaseHas('content_tag', [
            'tag_id' => $tag2->id
        ]);
        Storage::disk(config('filesystems.default'))->assertExists('images/'.$image->hashName());
    }
}
