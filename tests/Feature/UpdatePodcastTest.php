<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use App\Models\{Category, Podcast, Role, User};
use Tests\TestCase;

class UpdatePodcastTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Podcast not found
     */
    public function test_podcast_is_not_found()
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
        $response = $this->postJson(route('admin.podcasts.update', [
            'podcast' => 'non-existent-id'
        ]));

        $response->assertNotFound();
    }

    /**
     * Validation errors
     *
     * @return void
     */
    public function test_validation_errors_occur_while_updating_podcast()
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
        $podcast = Podcast::factory()
                        ->create();

        Sanctum::actingAs($admin, ['*']);
        $response = $this->postJson(route('admin.podcasts.update', [
            'podcast' => $podcast
        ]), [
            'name' => null
        ]);

        $response->assertInvalid(['name']);
        $response->assertUnprocessable();
    }

    /**
     * Podcast was updated successfully
     */
    public function test_podcast_was_successfully_updated()
    {
        $image = UploadedFile::fake()->create('image.png', 1024);
        $oldName = 'ladies lounge with Ademorayo';
        $newName = 'the female revolution';
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
        $podcast = Podcast::factory()
                        ->for($category1)
                        ->create([
                            'name' => $oldName
                        ]);

        Sanctum::actingAs($admin, ['*']);
        $response = $this->postJson(route('admin.podcasts.update', [
            'podcast' => $podcast
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
        $podcast->refresh();

        $response->assertOk();
        $response->assertValid();
        $this->assertSame($newName, $podcast->name);
        $this->assertSame($category2->id, $podcast->category_id);
        Storage::disk(config('filesystems.default'))->assertExists('podcasts/'.$image->hashName());
    }
}
