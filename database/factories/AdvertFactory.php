<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Advert;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Advert>
 */
class AdvertFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'url' => fake()->url(),
            'image' => fake()->url(),
            'file_driver' => config('filesystems.default'),
            'image_url' => fake()->url(),
            'position' => fake()->randomElement([
                Advert::TOP,
                Advert::BOTTOM,
                Advert::LEFT,
                Advert::RIGHT
            ])
        ];
    }
}
