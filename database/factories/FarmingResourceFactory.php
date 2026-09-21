<?php

namespace Database\Factories;

use App\Enums\FarmingResourceType;
use App\Enums\QuantityUnit;
use App\Models\FarmingResource;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FarmingResource>
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
            'rate' => fake()->randomFloat(2, 500, 15_000),
            'team_id' => Team::factory(),
        ];
    }

    public function fertilizer(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => FarmingResourceType::Fertilizer,
            'quantity_unit' => QuantityUnit::Sack,
            'rate' => 4_000,
        ]);
    }

    public function seed(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => FarmingResourceType::Seed,
            'quantity_unit' => QuantityUnit::Sack,
            'rate' => 6_000,
        ]);
    }

    public function implement(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => FarmingResourceType::Implement,
            'quantity_unit' => QuantityUnit::Hour,
            'rate' => 2_500,
        ]);
    }

    public function pesticide(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => FarmingResourceType::Pesticide,
            'quantity_unit' => QuantityUnit::Bottle,
            'rate' => 1_500,
        ]);
    }
}
