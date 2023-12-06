<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use App\Models\{Role, User};
use App\Exceptions\EmailUnregisteredException;
use App\Notifications\ResetPasswordNotification;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Validation errors
     *
     * @return void
     */
    public function test_validation_errors_occur_while_initiating_forgot_password()
    {
        $response = $this->postJson(route('auth.forgot-password'), [
            'email' => null
        ]);

        $response->assertInvalid(['email']);
        $response->assertUnprocessable();
    }

    /**
     * Email does not belong to a registered user
     *
     * @return void
     */
    public function test_email_does_not_belong_to_a_registered_user()
    {
        $this->withoutExceptionHandling();
        $this->expectException(EmailUnregisteredException::class);

        $response = $this->postJson(route('auth.forgot-password'), [
            'email' => fake()->email(),
        ]);
    }

    /**
     * Forgot password was successfully initiated
     */
    public function test_forgot_password_was_successfully_initiated()
    {
        Notification::fake();
        $role = Role::factory()
                    ->user()
                    ->create();
        $user = User::factory()
                    ->users()
                    ->create();

        $response = $this->postJson(route('auth.forgot-password'), [
            'email' => $user->email,
        ]);
        $user->refresh();

        $this->assertNotNull($user->reset_password_verification);
        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }
}
