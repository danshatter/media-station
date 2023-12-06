<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Role;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            //
        ];
    }

    /**
     * User role
     *
     * @return static
     */
    public function user()
    {
        return $this->state(function (array $attributes) {
            return [
                'id' => Role::USER,
                'name' => 'user'
            ];
        });
    }

    /**
     * Administrator role
     *
     * @return static
     */
    public function administrator()
    {
        return $this->state(function (array $attributes) {
            return [
                'id' => Role::ADMINISTRATOR,
                'name' => 'administrator',
            ];
        });
    }
}
