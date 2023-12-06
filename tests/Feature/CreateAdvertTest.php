<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use App\Models\{Advert, Role, User};
use Tests\TestCase;

class CreateAdvertTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Validation errors
     *
     * @return void
     */
    public function test_validation_errors_occur_while_creating_advert()
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
        $response = $this->postJson(route('admin.adverts.store'), [
            'image' => null
        ]);

        $response->assertInvalid(['image']);
        $response->assertUnprocessable();
    }

    /**
     * Advert was created successfully
     */
    public function test_advert_was_successfully_created()
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

        Sanctum::actingAs($admin, ['*']);
        $response = $this->postJson(route('admin.adverts.store'), [
            'image' => $image,
            'url' => fake()->url(),
            'position' => fake()->randomElement([
                Advert::TOP,
                Advert::BOTTOM,
                Advert::LEFT,
                Advert::RIGHT
            ])
        ]);

        $response->assertValid();
        $response->assertCreated();
        $this->assertDatabaseCount('adverts', 1);
        Storage::disk(config('filesystems.default'))->assertExists('adverts/'.$image->hashName());
    }
}
