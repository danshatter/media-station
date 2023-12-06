<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{Role, User};
use Tests\TestCase;

class SearchContentsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Validation errors
     *
     * @return void
     */
    public function test_validation_errors_occur_while_searching_contents()
    {
        $userRole = Role::factory()
                        ->user()
                        ->create();
        $adminRole = Role::factory()
                        ->administrator()
                        ->create();
        $user = User::factory()
                    ->users()
                    ->create();

        $response = $this->getJson(route('search-contents'));

        $response->assertInvalid(['title']);
        $response->assertUnprocessable();
    }

    /**
     * Search content results were successfully fetched
     */
    public function test_search_content_results_were_successfully_fetched()
    {
        $userRole = Role::factory()
                    ->user()
                    ->create();
        $adminRole = Role::factory()
                        ->administrator()
                        ->create();
        $users = User::factory()
                    ->users()
                    ->create();

        $response = $this->getJson(route('search-contents', [
            'title' => fake()->word()
        ]));

        $response->assertOk();
    }
}
