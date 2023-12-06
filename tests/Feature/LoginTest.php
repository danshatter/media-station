<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Models\{Role, User};
use App\Exceptions\InvalidCredentialsException;
use App\Services\Application as ApplicationService;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Validation errors
     *
     * @return void
     */
    public function test_validation_errors_occur_during_user_login()
    {
        $response = $this->postJson(route('auth.login'), [
            'email' => null
        ]);

        $response->assertInvalid(['email']);
        $response->assertUnprocessable();
    }

    /**
     * Invalid credentials
     *
     * @return void
     */
    public function test_user_provided_invalid_credentials()
    {
        $this->withoutExceptionHandling();
        $this->expectException(InvalidCredentialsException::class);
        $password = 'password';
        $inputtedPassword = 'wrongpassword';
        $role = Role::factory()
                    ->user()
                    ->create();
        $user = User::factory()
                    ->users()
                    ->create([
                        'password' => Hash::make($password)
                    ]);

        $response = $this->postJson(route('auth.login'), [
            'email' => $user->email,
            'password' => app()->make(ApplicationService::class)->encryptPasswordString($inputtedPassword)
        ]);
    }

    /**
     * Login successful
     *
     * @return void
     */
    public function test_login_was_successful()
    {
        $password = 'password';
        $role = Role::factory()
                    ->user()
                    ->create();
        $user = User::factory()
                    ->users()
                    ->create([
                        'password' => Hash::make($password)
                    ]);

        $response = $this->postJson(route('auth.login'), [
            'email' => $user->email,
            'password' => app()->make(ApplicationService::class)->encryptPasswordString($password)
        ]);

        $response->assertValid();
        $response->assertOk();
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }
}
