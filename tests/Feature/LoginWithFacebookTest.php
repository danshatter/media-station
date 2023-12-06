<?php

namespace Tests\Feature;

use Laravel\Socialite\Facades\Socialite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use App\Models\Role;
use Tests\TestCase;

class LoginWithFacebookTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Validation errors
     *
     * @return void
     */
    public function test_validation_errors_occur_during_user_login_with_facebook()
    {
        $response = $this->postJson(route('auth.login-with-facebook'), [
            'access_token' => null
        ]);

        $response->assertInvalid(['access_token']);
        $response->assertUnprocessable();
    }

    /**
     * Login with Facebook successful
     *
     * @return void
     */
    public function test_login_with_facebook_was_successful()
    {
        $email = 'oluyemi@renners.ng';
        $role = Role::factory()
                    ->user()
                    ->create();
        Socialite::shouldReceive('driver->fields->scopes->userFromToken->getRaw')
                ->once()
                ->andReturn([
                    'first_name' => fake()->firstName(),
                    'last_name' => fake()->lastName(),
                    'email' => $email,
                ]);
        
        $response = $this->postJson(route('auth.login-with-facebook'), [
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
