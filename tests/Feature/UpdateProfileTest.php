<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\{Role, User};
use Tests\TestCase;

class UpdateProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Validation errors
     *
     * @return void
     */
    public function test_validation_errors_occur_while_updating_profile()
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
        $response = $this->putJson(route('profile'), [
            'phone' => null
        ]);

        $response->assertInvalid(['phone']);
        $response->assertUnprocessable();
    }

    /**
     * Profile was updated successfully
     */
    public function test_profile_was_updated_successfully()
    {
        $phone = fake()->e164PhoneNumber();
        $userRole = Role::factory()
                        ->user()
                        ->create();
        $adminRole = Role::factory()
                        ->administrator()
                        ->create();
        $user = User::factory()
                    ->users()
                    ->create([
                        'phone' => null
                    ]);

        Sanctum::actingAs($user, ['*']);
        $response = $this->putJson(route('profile'), [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone' => $phone,
        ]);
        $user->refresh();

        $response->assertValid();
        $response->assertOk();
        $this->assertNotNull($user->phone);
    }
}
