<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\{Advert, Role, User};
use Tests\TestCase;

class DeleteAdvertTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Advert not found
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
        $response = $this->deleteJson(route('admin.adverts.show', [
            'advert' => 'non-existent-id'
        ]));

        $response->assertNotFound();
    }

    /**
     * Advert was successfully deleted
     */
    public function test_advert_was_successfully_deleted()
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
        $response = $this->deleteJson(route('admin.adverts.destroy', [
            'advert' => $advert
        ]));

        $response->assertOk();
        $this->assertModelMissing($advert);
    }
}
