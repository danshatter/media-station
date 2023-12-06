<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use App\Models\Role;
use App\Notifications\SendUserEmailVerificationNotification;
use App\Services\Application as ApplicationService;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Validation errors
     *
     * @return void
     */
    public function test_validation_errors_occur_during_user_registration()
    {
        $response = $this->postJson(route('auth.register'), [
            'first_name' => null
        ]);

        $response->assertInvalid(['first_name']);
        $response->assertUnprocessable();
    }

    /**
     * User registration successful
     *
     * @return void
     */
    public function test_user_registration_was_successful()
    {
        Notification::fake();
        $role = Role::factory()
                    ->user()
                    ->create();
        $password = 'password';

        $response = $this->postJson(route('auth.register'), [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->email(),
            'password' => app()->make(ApplicationService::class)->encryptPasswordString($password),
            'password_confirmation' => app()->make(ApplicationService::class)->encryptPasswordString($password)
        ]);

        $response->assertValid();
        $response->assertCreated();
        $this->assertDatabaseHas('users', [
            'role_id' => $role->id
        ]);
        Notification::assertTimesSent(1, SendUserEmailVerificationNotification::class);
    }
}
