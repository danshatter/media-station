<?php

namespace Tests\Feature;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{Role, User};
use App\Exceptions\{EmailUnregisteredException, ResetPasswordVerificationLinkExpiredException};
use App\Services\Application as ApplicationService;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Validation errors
     *
     * @return void
     */
    public function test_validation_errors_occur_while_resetting_password()
    {
        $response = $this->putJson(route('auth.reset-password'), [
            'token' => null
        ]);

        $response->assertInvalid(['token']);
        $response->assertUnprocessable();
    }

    /**
     * User could not be found
     *
     * @return void
     */
    public function test_user_could_not_be_found()
    {
        $this->withoutExceptionHandling();
        $this->expectException(EmailUnregisteredException::class);
        $password = 'password';
        $role = Role::factory()
                    ->user()
                    ->create();
        $user = User::factory()
                    ->users()
                    ->create();

        $response = $this->putJson(route('auth.reset-password'), [
            'password' => app()->make(ApplicationService::class)->encryptPasswordString($password),
            'password_confirmation' => app()->make(ApplicationService::class)->encryptPasswordString($password),
            'token' => 'faketoken',
        ]);
    }

    /**
     * Reset password link expired
     */
    public function test_reset_password_link_has_expired()
    {
        $this->withoutExceptionHandling();
        $this->expectException(ResetPasswordVerificationLinkExpiredException::class);
        $oldPassword = 'password';
        $newPassword = 'newpassword';
        $role = Role::factory()
                    ->user()
                    ->create();
        $user = User::factory()
                    ->users()
                    ->create([
                        'password' => Hash::make($oldPassword),
                        'reset_password_verification' => Str::random(20),
                        'reset_password_verification_expires_at' => now()->subMinutes(5)
                    ]);

        $response = $this->putJson(route('auth.reset-password'), [
            'password' => app()->make(ApplicationService::class)->encryptPasswordString($newPassword),
            'password_confirmation' => app()->make(ApplicationService::class)->encryptPasswordString($newPassword),
            'token' => $user->reset_password_verification,            
        ]);
    }

    /**
     * Password reset successfully
     */
    public function test_password_was_reset_successfully()
    {
        $oldPassword = 'password';
        $newPassword = 'newpassword';
        $role = Role::factory()
                    ->user()
                    ->create();
        $user = User::factory()
                    ->users()
                    ->create([
                        'password' => Hash::make($oldPassword),
                        'reset_password_verification' => Str::random(20)
                    ]);
        $oldPasswordHash = $user->password;

        $response = $this->putJson(route('auth.reset-password'), [
            'password' => app()->make(ApplicationService::class)->encryptPasswordString($newPassword),
            'password_confirmation' => app()->make(ApplicationService::class)->encryptPasswordString($newPassword),
            'token' => $user->reset_password_verification,            
        ]);
        $user->refresh();

        $this->assertNull($user->reset_password_verification);
        $this->assertNotSame($oldPasswordHash, $user->password);
    }
}
