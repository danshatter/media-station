<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use App\Models\{Advert, Role, User};
use Tests\TestCase;

class UpdateAdvertTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Show not found
     */
    public function test_advert_is_not_found()
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
        $response = $this->postJson(route('admin.adverts.update', [
            'advert' => 'non-existent-id'
        ]));

        $response->assertNotFound();
    }

    /**
     * Validation errors
     *
     * @return void
     */
    public function test_validation_errors_occur_while_updating_advert()
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
        $advert = Advert::factory()
                        ->create();

        Sanctum::actingAs($admin, ['*']);
        $response = $this->postJson(route('admin.adverts.update', [
            'advert' => $advert
        ]), [
            'url' => null
        ]);

        $response->assertInvalid(['url']);
        $response->assertUnprocessable();
    }

    /**
     * Advert was updated successfully
     */
    public function test_advert_was_successfully_updated()
    {
        $image = UploadedFile::fake()->create('image.png', 1024);
        $oldPosition = Advert::TOP;
        $newPosition = Advert::BOTTOM;
        $userRole = Role::factory()
                        ->user()
                        ->create();
        $adminRole = Role::factory()
                        ->administrator()
                        ->create();
        $admin = User::factory()
                    ->administrators()
                    ->create();
        $advert = Advert::factory()
                        ->create([
                            'position' => $oldPosition
                        ]);

        Sanctum::actingAs($admin, ['*']);
        $response = $this->postJson(route('admin.adverts.update', [
            'advert' => $advert
        ]), [
            'position' => $newPosition,
            'image' => $image,
            'url' => fake()->url()
        ]);
        $advert->refresh();

        $response->assertOk();
        $response->assertValid();
        $this->assertSame($newPosition, $advert->position);
        Storage::disk(config('filesystems.default'))->assertExists('adverts/'.$image->hashName());
    }
}
