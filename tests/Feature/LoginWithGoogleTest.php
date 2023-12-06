<?php

namespace Tests\Feature;

use Laravel\Socialite\Facades\Socialite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use App\Models\Role;
use Tests\TestCase;

class LoginWithGoogleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Validation errors
     *
     * @return void
     */
    public function test_validation_errors_occur_during_user_login_with_google()
    {
        $response = $this->postJson(route('auth.login-with-google'), [
            'access_token' => null
        ]);

        $response->assertInvalid(['access_token']);
        $response->assertUnprocessable();
    }

    /**
     * Login with Google successful
     *
     * @return void
     */
    public function test_login_with_google_was_successful()
    {
        $email = 'oluyemi@renners.ng';
        $role = Role::factory()
                    ->user()
                    ->create();
        Socialite::shouldReceive('driver->userFromToken->getRaw')
                ->once()
                ->andReturn([
                    'family_name' => fake()->lastName(),
                    'sub' => fake()->numberBetween(1, 10000000),
                    'picture' => fake()->url(),
                    'locale' => 'en',
                    'email_verified' => true,
                    'given_name' => fake()->firstName(),
                    'email' => $email,
                    'name' => fake()->name()
                ]);
        
        $response = $this->postJson(route('auth.login-with-google'), [
            'access_token' => Str::random(40)
        ]);

        $response->assertValid();
        $response->assertOk();
        $this->assertDatabaseCount('personal_access_tokens', 1);
        $this->assertDatabaseHas('users', [
            'email' => $email
        ]);
    }
}
