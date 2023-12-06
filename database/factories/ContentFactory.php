<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\{Content, Category, Podcast, Show};

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Content>
 */
class ContentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $contentable = $this->contentable();

        return [
            'contentable_id' => $contentable::factory(),
            'contentable_type' => $contentable,
            'guid' => fake()->uuid(),
            'published_at' => now()->subDays(3),
            'enclosure_url' => fake()->unique()->url(),
            'type' => fake()->word(),
            'author' => fake()->name(),
            'subtitle' => fake()->sentence(),
            'summary' => fake()->sentence(),
            'duration' => '50 minutes',
            'explicit' => fake()->word(),
            'season' => fake()->numberBetween(1, 9),
            'episode_type' => fake()->word()
        ];
    }

    /**
     * The contentable model
     */
    private function contentable()
    {
        return fake()->randomElement([
            Podcast::class,
            Show::class
        ]);
    }
}
