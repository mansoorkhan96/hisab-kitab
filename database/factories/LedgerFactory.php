<?php

namespace Database\Factories;

use App\Models\CropSeason;
use App\Models\User;
use App\Models\FarmingResource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ledger>
 */
class LedgerFactory extends Factory
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
            'user_id' => User::factory(),
            'farming_resource_id' => FarmingResource::factory(),
            'quantity' => random_int(1, 3),
            'rate' => null,
        ];
    }
}
