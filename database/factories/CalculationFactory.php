<?php

namespace Database\Factories;

use App\Enums\CropType;
use App\Models\Calculation;
use App\Models\CropSeason;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Calculation>
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
            'user_id' => User::factory(),
        ];
    }

    public function wheat(): static
    {
        return $this->state(fn (array $attributes) => [
            'crop_type' => CropType::Wheat,
            'kudhi_in_kgs' => 1,
            'kamdari' => 0,
            'wheat_straw_rate' => 310,
        ]);
    }

    public function cotton(): static
    {
        return $this->state(fn (array $attributes) => [
            'crop_type' => CropType::Cotton,
            'kudhi_in_kgs' => 0,
            'kamdari' => 0,
            'wheat_straw_rate' => null,
        ]);
    }
}
