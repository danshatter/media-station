<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{Role, User};
use Tests\TestCase;

class GetCategoriesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Categories were successfully fetched
     */
    public function test_categories_were_successfully_fetched()
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

        $response = $this->getJson(route('categories.index'));

        $response->assertOk();
    }
}
