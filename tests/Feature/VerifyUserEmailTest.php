<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use App\Models\{Role, User};
use Tests\TestCase;

class VerifyUserEmailTest extends TestCase
{
    use RefreshDatabase;

    /**
     * No token in URL
     */
    public function test_no_token_was_present_in_url()
    {
        $response = $this->get(route('auth.verify-email'));

        $response->assertSee(__('app.email_verification_failed'));
    }

    /**
     * No user with verification
     */
    public function test_no_user_has_supplied_verification_token()
    {
        $response = $this->get(route('auth.verify-email', [
            'token' => Str::random(20)
        ]));

        $response->assertSee(__('app.email_verification_failed'));
    }

    /**
     * Email has already been verified
     */
    public function test_user_has_already_been_verified()
    {
        $role = Role::factory()
                    ->user()
                    ->create();
        $user = User::factory()
                    ->users()
                    ->create([
                        'verification' => Str::random(20)
                    ]);

        $response = $this->get(route('auth.verify-email', [
            'token' => $user->verification
        ]));

        $response->assertSee(__('app.email_already_verified'));
    }

    /**
     * Verification link has expired
     */
    public function test_user_verification_link_has_expired()
    {
        $role = Role::factory()
                    ->user()
                    ->create();
        $user = User::factory()
                    ->users()
                    ->unverified()
                    ->create([
                        'verification' => Str::random(20),
                        'verification_expires_at' => now()->subMinutes(5)
                    ]);

        $response = $this->get(route('auth.verify-email', [
            'token' => $user->verification
        ]));

        $response->assertSee(__('app.verification_link_expired'));
    }

    /**
     * Email verification successful
     */
    public function test_email_was_successfully_verified()
    {
        $role = Role::factory()
                    ->user()
                    ->create();
        $user = User::factory()
                    ->users()
                    ->unverified()
                    ->create([
                        'verification' => Str::random(20)
                    ]);

        $response = $this->get(route('auth.verify-email', [
            'token' => $user->verification
        ]));
        $user->refresh();

        $this->assertNotNull($user->email_verified_at);
    }
}
