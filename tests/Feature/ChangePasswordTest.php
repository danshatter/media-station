<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use App\Models\{Role, User};
use App\Services\Application as ApplicationService;
use Tests\TestCase;

class ChangePasswordTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Validation errors
     *
     * @return void
     */
    public function test_validation_errors_occur_while_changing_password()
    {
        $userRole = Role::factory()
                        ->user()
                        ->create();
        $adminRole = Role::factory()
                        ->administrator()
                        ->create();
        $user = User::factory()
                    ->users()
                    ->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->putJson(route('auth.change-password'), [
            'current_password' => null
        ]);

        $response->assertInvalid(['current_password']);
        $response->assertUnprocessable();
    }

    /**
     * Password changed successfully
     */
    public function test_password_was_changed_successfully()
    {
        $oldPassword = 'password';
        $newPassword = 'newpassword';
        $userRole = Role::factory()
                        ->user()
                        ->create();
        $adminRole = Role::factory()
                        ->administrator()
                        ->create();
        $user = User::factory()
                    ->users()
                    ->create([
                        'password' => Hash::make($oldPassword)
                    ]);
        $oldHashedPassword = $user->password;

        Sanctum::actingAs($user, ['*']);
        $response = $this->putJson(route('auth.change-password'), [
            'current_password' => app()->make(ApplicationService::class)->encryptPasswordString($oldPassword),
            'password' => app()->make(ApplicationService::class)->encryptPasswordString($newPassword),
            'password_confirmation' => app()->make(ApplicationService::class)->encryptPasswordString($newPassword)
        ]);
        $user->refresh();

        $response->assertValid();
        $response->assertOk();
        $this->assertNotSame($oldHashedPassword, $user->password);
    }
}
