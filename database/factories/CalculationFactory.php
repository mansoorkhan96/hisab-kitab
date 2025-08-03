<?php

namespace Database\Factories;

use App\Models\CropSeason;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Calculations>
 */
class CalculationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'crop_season_id' => CropSeason::factory(),
            'team_id' => Team::factory(),
        ];
    }
}
