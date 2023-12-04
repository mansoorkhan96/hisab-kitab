<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Enums\FarmingResourceType;
use App\Enums\QuantityUnit;
use App\Models\CropSeason;
use App\Models\Farmer;
use App\Models\FarmingResource;
use App\Models\Ledger;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = \App\Models\User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $farmingResources = $this->seedFarmingResources($user);

        $cropSeason = CropSeason::factory()->for($user)->recycle($user)->create();

        $farmers = Farmer::factory(5)->for($user)->create();

        Ledger::factory(100)->recycle($cropSeason)->create([
            'farmer_id' => fn () => $farmers->random()->id,
            'farming_resource_id' => fn () => $farmingResources->random()->id,
        ]);
    }

    public function seedFarmingResources(User $user)
    {
        return FarmingResource::factory()->for($user)->createMany([
            [
                'name' => 'DAP',
                'type' => FarmingResourceType::Fertilizer,
                'quantity_unit' => QuantityUnit::Sack,
                'rate' => random_int(3000, 6000),
            ],
            [
                'name' => 'Urea',
                'type' => FarmingResourceType::Fertilizer,
                'quantity_unit' => QuantityUnit::Sack,
                'rate' => random_int(3000, 6000),
            ],

            [
                'name' => 'Sataar Bij',
                'type' => FarmingResourceType::Seed,
                'quantity_unit' => QuantityUnit::Sack,
                'rate' => random_int(3000, 6000),
            ],

            [
                'name' => 'Raja',
                'type' => FarmingResourceType::Implement,
                'quantity_unit' => QuantityUnit::Hour,
                'rate' => random_int(3000, 6000),
            ],
            [
                'name' => 'Kean',
                'type' => FarmingResourceType::Implement,
                'quantity_unit' => QuantityUnit::Hour,
                'rate' => random_int(3000, 6000),
            ],
        ]);
    }
}
