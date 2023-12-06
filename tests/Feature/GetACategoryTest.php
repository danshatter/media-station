<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{Category, Role, User};
use Tests\TestCase;

class GetACategoryTest extends TestCase
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
        
        $response = $this->getJson(route('categories.show', [
            'category' => 'non-existent-id'
        ]));

        $response->assertNotFound();
    }

    /**
     * Category was successfully fetched
     */
    public function test_category_was_successfully_fetched()
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
        
        $response = $this->getJson(route('categories.show', [
            'category' => $category
        ]));

        $response->assertOk();
    }
}
