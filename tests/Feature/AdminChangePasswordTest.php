<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use App\Models\{Role, User};
use App\Services\Application as ApplicationService;
use Tests\TestCase;

class AdminChangePasswordTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Validation errors
     *
     * @return void
     */
    public function test_validation_errors_occur_while_changing_admin_password()
    {
        $userRole = Role::factory()
                        ->user()
                        ->create();
        $adminRole = Role::factory()
                        ->administrator()
                        ->create();
        $admin = User::factory()
                    ->administrators()
                    ->create();

        Sanctum::actingAs($admin, ['*']);
        $response = $this->putJson(route('auth.admin.change-password'), [
            'current_password' => null
        ]);

        $response->assertInvalid(['current_password']);
        $response->assertUnprocessable();
    }

    /**
     * Admin password changed successfully
     */
    public function test_admin_password_was_changed_successfully()
    {
        $oldPassword = 'password';
        $newPassword = 'newpassword';
        $userRole = Role::factory()
                        ->user()
                        ->create();
        $adminRole = Role::factory()
                        ->administrator()
                        ->create();
        $admin = User::factory()
                    ->administrators()
                    ->create([
                        'password' => Hash::make($oldPassword)
                    ]);
        $oldHashedPassword = $admin->password;

        Sanctum::actingAs($admin, ['*']);
        $response = $this->putJson(route('auth.admin.change-password'), [
            'current_password' => app()->make(ApplicationService::class)->encryptPasswordString($oldPassword),
            'password' => app()->make(ApplicationService::class)->encryptPasswordString($newPassword),
            'password_confirmation' => app()->make(ApplicationService::class)->encryptPasswordString($newPassword)
        ]);
        $admin->refresh();

        $response->assertValid();
        $response->assertOk();
        $this->assertNotSame($oldHashedPassword, $admin->password);
    }
}
