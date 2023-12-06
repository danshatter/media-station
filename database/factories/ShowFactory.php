<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Show>
 */
class ShowFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'category_id' => Category::factory(),
            'name' => fake()->unique()->words(3, true),
            'description' => fake()->sentence(),
            'link' => fake()->url(),
            'owner' => [
                'name' => fake()->name(),
                'email' => fake()->email(),
            ],
            'subtitle' => fake()->sentence(),
            'summary' => fake()->sentence(),
            'explicit' => fake()->word(),
            'type' => fake()->word(),
        ];
    }
}
