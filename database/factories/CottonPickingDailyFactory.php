<?php

namespace Database\Factories;

use App\Models\CottonPickingDaily;
use App\Models\CottonPickingRound;
use App\Models\Labourer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CottonPickingDaily>
 */
class CottonPickingDailyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cotton_picking_round_id' => CottonPickingRound::factory(),
            'labourer_id' => Labourer::factory(),
            'picking_date' => fake()->date(),
            'kgs_picked' => fake()->numberBetween(1, 100),
        ];
    }
}
