<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use App\Models\{Role, User};
use App\Exceptions\{EmailAlreadyVerifiedException, EmailUnregisteredException};
use App\Notifications\SendUserEmailVerificationNotification;
use Tests\TestCase;

class ResendUserVerificationEmailTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Validation errors
     *
     * @return void
     */
    public function test_validation_errors_occur_while_resending_user_verification_email()
    {
        $response = $this->postJson(route('auth.resend-verification-email'), [
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

        $response = $this->postJson(route('auth.resend-verification-email'), [
            'email' => fake()->email(),
        ]);
    }

    /**
     * Email is already verified
     */
    public function test_email_is_already_verified()
    {
        $this->withoutExceptionHandling();
        $this->expectException(EmailAlreadyVerifiedException::class);
        $role = Role::factory()
                    ->user()
                    ->create();
        $user = User::factory()
                    ->users()
                    ->create([
                        'email_verified_at' => now()
                    ]);

        $response = $this->postJson(route('auth.resend-verification-email'), [
            'email' => $user->email,
        ]);
    }

    /**
     * Verification email was successfully sent
     */
    public function test_verification_email_was_successfully_sent()
    {
        Notification::fake();
        $role = Role::factory()
                    ->user()
                    ->create();
        $user = User::factory()
                    ->users()
                    ->create([
                        'email_verified_at' => null
                    ]);

        $response = $this->postJson(route('auth.resend-verification-email'), [
            'email' => $user->email,
        ]);
        $user->refresh();

        $this->assertNotNull($user->verification);
        Notification::assertSentTo($user, SendUserEmailVerificationNotification::class);
    }
}
