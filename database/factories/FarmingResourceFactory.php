<?php

namespace Database\Factories;

use App\Enums\FarmingResourceType;
use App\Enums\QuantityUnit;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Resource>
 */
class FarmingResourceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->word(),
            'type' => fake()->randomElement(FarmingResourceType::cases()),
            'quantity_unit' => fake()->randomElement(QuantityUnit::cases()),
            'team_id' => Team::factory(),
        ];
    }
}
