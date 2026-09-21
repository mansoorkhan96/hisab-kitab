<?php

namespace Database\Factories;

use App\Models\CropSeason;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CropSeason>
 */
class CropSeasonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->unique()->word(),
            'team_id' => Team::factory(),
            'is_current' => false,
        ];
    }

    public function current(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_current' => true,
            'wheat_rate' => 4_000,
            'wheat_straw_rate' => 100,
            'cotton_rate_per_kg' => 8_000,
            'cotton_labour_rate_per_kg' => 25,
        ]);
    }
}
