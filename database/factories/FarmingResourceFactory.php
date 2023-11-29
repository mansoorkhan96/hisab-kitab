<?php

namespace Database\Factories;

use App\Enums\FarmingResourceType;
use App\Enums\QuantityUnit;
use App\Models\User;
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
            // 'user_id' => User::factory(),
            'name' => fake()->word(),
            'type' => fake()->randomElement(FarmingResourceType::values()),
            'quantity_unit' => fake()->randomElement(QuantityUnit::values()),
        ];
    }
}
