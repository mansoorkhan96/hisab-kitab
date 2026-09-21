<?php

namespace Database\Factories;

use App\Models\CottonPickingRound;
use App\Models\CropSeason;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CottonPickingRound>
 */
class CottonPickingRoundFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'crop_season_id' => CropSeason::factory(),
            'user_id' => User::factory(),
            'title' => fake()->words(2, true),
        ];
    }
}
