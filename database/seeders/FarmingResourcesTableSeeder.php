<?php

namespace Database\Seeders;

use App\Enums\FarmingResourceType;
use App\Enums\QuantityUnit;
use App\Models\FarmingResource;
use Illuminate\Database\Seeder;

class FarmingResourcesTableSeeder extends Seeder
{
    public function run(): void
    {
        FarmingResource::factory()->createMany([
            [
                'name' => 'DAP',
                'type' => FarmingResourceType::Fertilizer,
                'quantity_unit' => QuantityUnit::Sack,
            ],
            [
                'name' => 'Urea',
                'type' => FarmingResourceType::Fertilizer,
                'quantity_unit' => QuantityUnit::Sack,
            ],

            [
                'name' => 'Sataar Bij',
                'type' => FarmingResourceType::Seed,
                'quantity_unit' => QuantityUnit::Sack,
            ],

            [
                'name' => 'Raja',
                'type' => FarmingResourceType::Implement,
                'quantity_unit' => QuantityUnit::Hour,
            ],
            [
                'name' => 'Kean',
                'type' => FarmingResourceType::Implement,
                'quantity_unit' => QuantityUnit::Hour,
            ],
        ]);
    }
}
