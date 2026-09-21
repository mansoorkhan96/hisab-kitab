<?php

namespace Database\Factories;

use App\Models\Calculation;
use App\Models\Threshing;
use App\Models\Tractor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Threshing>
 */
class ThreshingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'calculation_id' => Calculation::factory(),
            'tractor_id' => Tractor::factory(),
            'total_wheat_sacks' => fake()->randomFloat(2, 10, 150),
        ];
    }
}
