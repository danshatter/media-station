<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Role;
use App\Services\Application as ApplicationService;
use Tests\TestCase;

class AdminRegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Validation errors
     *
     * @return void
     */
    public function test_validation_errors_occur_during_admin_registration()
    {
        $response = $this->postJson(route('auth.admin.register'), [
            'first_name' => null
        ]);

        $response->assertInvalid(['first_name']);
        $response->assertUnprocessable();
    }

    /**
     * Admin registration successful
     *
     * @return void
     */
    public function test_admin_registration_was_successful()
    {
        $userRole = Role::factory()
                        ->user()
                        ->create();
        $adminRole = Role::factory()
                        ->administrator()
                        ->create();
        $password = 'password';

        $response = $this->postJson(route('auth.admin.register'), [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->email(),
            'password' => app()->make(ApplicationService::class)->encryptPasswordString($password),
            'password_confirmation' => app()->make(ApplicationService::class)->encryptPasswordString($password)
        ]);

        $response->assertValid();
        $response->assertCreated();
        $this->assertDatabaseHas('users', [
            'role_id' => $adminRole->id
        ]);
    }
}
