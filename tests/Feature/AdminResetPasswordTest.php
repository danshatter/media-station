<?php

namespace Tests\Feature;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{Role, User};
use App\Exceptions\{AdminEmailUnregisteredException, ResetPasswordVerificationLinkExpiredException};
use App\Services\Application as ApplicationService;
use Tests\TestCase;

class AdminResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Validation errors
     *
     * @return void
     */
    public function test_validation_errors_occur_while_resetting_admin_password()
    {
        $response = $this->putJson(route('auth.admin.reset-password'), [
            'token' => null
        ]);

        $response->assertInvalid(['token']);
        $response->assertUnprocessable();
    }

    /**
     * Admin could not be found
     *
     * @return void
     */
    public function test_admin_could_not_be_found()
    {
        $this->withoutExceptionHandling();
        $this->expectException(AdminEmailUnregisteredException::class);
        $password = 'password';
        $adminRole = Role::factory()
                        ->administrator()
                        ->create();
        $admin = User::factory()
                    ->administrators()
                    ->create();

        $response = $this->putJson(route('auth.admin.reset-password'), [
            'password' => app()->make(ApplicationService::class)->encryptPasswordString($password),
            'password_confirmation' => app()->make(ApplicationService::class)->encryptPasswordString($password),
            'token' => 'faketoken',
        ]);
    }

    /**
     * Reset password link expired
     */
    public function test_admin_reset_password_link_has_expired()
    {
        $this->withoutExceptionHandling();
        $this->expectException(ResetPasswordVerificationLinkExpiredException::class);
        $oldPassword = 'password';
        $newPassword = 'newpassword';
        $adminRole = Role::factory()
                        ->administrator()
                        ->create();
        $admin = User::factory()
                    ->administrators()
                    ->create([
                        'password' => Hash::make($oldPassword),
                        'reset_password_verification' => Str::random(20),
                        'reset_password_verification_expires_at' => now()->subMinutes(5)
                    ]);

        $response = $this->putJson(route('auth.admin.reset-password'), [
            'password' => app()->make(ApplicationService::class)->encryptPasswordString($newPassword),
            'password_confirmation' => app()->make(ApplicationService::class)->encryptPasswordString($newPassword),
            'token' => $admin->reset_password_verification,            
        ]);
    }

    /**
     * Admin password reset successfully
     */
    public function test_admin_password_was_reset_successfully()
    {
        $oldPassword = 'password';
        $newPassword = 'newpassword';
        $adminRole = Role::factory()
                        ->administrator()
                        ->create();
        $admin = User::factory()
                    ->administrators()
                    ->create([
                        'password' => Hash::make($oldPassword),
                        'reset_password_verification' => Str::random(20)
                    ]);
        $oldPasswordHash = $admin->password;

        $response = $this->putJson(route('auth.admin.reset-password'), [
            'password' => app()->make(ApplicationService::class)->encryptPasswordString($newPassword),
            'password_confirmation' => app()->make(ApplicationService::class)->encryptPasswordString($newPassword),
            'token' => $admin->reset_password_verification,            
        ]);
        $admin->refresh();

        $this->assertNull($admin->reset_password_verification);
        $this->assertNotSame($oldPasswordHash, $admin->password);
    }
}
