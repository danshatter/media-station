<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use App\Models\{Role, User};
use App\Exceptions\AdminEmailUnregisteredException;
use App\Notifications\AdminResetPasswordNotification;
use Tests\TestCase;

class AdminForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Validation errors
     *
     * @return void
     */
    public function test_validation_errors_occur_while_initiating_admin_forgot_password()
    {
        $response = $this->postJson(route('auth.admin.forgot-password'), [
            'email' => null
        ]);

        $response->assertInvalid(['email']);
        $response->assertUnprocessable();
    }

    /**
     * Email does not belong to a registered administrator
     *
     * @return void
     */
    public function test_email_does_not_belong_to_a_registered_administrator()
    {
        $this->withoutExceptionHandling();
        $this->expectException(AdminEmailUnregisteredException::class);

        $response = $this->postJson(route('auth.admin.forgot-password'), [
            'email' => fake()->email(),
        ]);
    }

    /**
     * Admin forgot password was successfully initiated
     */
    public function test_admin_forgot_password_was_successfully_initiated()
    {
        Notification::fake();
        $adminRole = Role::factory()
                        ->administrator()
                        ->create();
        $admin = User::factory()
                    ->administrators()
                    ->create();

        $response = $this->postJson(route('auth.admin.forgot-password'), [
            'email' => $admin->email,
        ]);
        $admin->refresh();

        $this->assertNotNull($admin->reset_password_verification);
        Notification::assertSentTo($admin, AdminResetPasswordNotification::class);
    }
}
