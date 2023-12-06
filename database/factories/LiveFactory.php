<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Live;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Live>
 */
class LiveFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'type' => fake()->randomElement([
                Live::RADIO
            ]),
            'link' => fake()->url()
        ];
    }
}
