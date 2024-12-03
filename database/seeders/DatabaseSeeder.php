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
    public $user;

    public $farmingResources;

    public $cropSeason;

    public function run(): void
    {
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->farmingResources = $this->seedFarmingResources($this->user);

        $this->cropSeason = CropSeason::factory()
            ->for($this->user)
            ->recycle($this->user)
            ->create(['name' => 'Season-'.now()->year]);

        $this->seedAshrafRind();
    }

    public function seedAshrafRind()
    {
        $ashraf = Farmer::factory()
            ->for($this->user)
            ->create(['name' => 'Ashraf Rind']);

        collect([
            ['farming_resource' => 'DAP', 'quantity' => 4.00, 'rate' => 9000.00],
            ['farming_resource' => 'Urea', 'quantity' => 21.00, 'rate' => 3000.00],
            ['farming_resource' => 'Palaas', 'quantity' => 5, 'rate' => 1800.00],
            ['farming_resource' => 'Bijj', 'quantity' => 10.50, 'rate' => 6000.00],
            ['farming_resource' => 'Kean', 'quantity' => 8.00, 'rate' => 2700.00],
            ['farming_resource' => 'Banna', 'quantity' => 8.00, 'rate' => 500.00],
            ['farming_resource' => 'Cultivator', 'quantity' => 8.00, 'rate' => 2700.00],
        ])->each(fn (array $ledger) => Ledger::factory()
            ->for($this->cropSeason)
            ->for($ashraf)
            ->for($this->farmingResources->where('name', $ledger['farming_resource'])->first())
            ->create(['quantity' => $ledger['quantity'], 'rate' => $ledger['rate']]));
    }

    public function seedFarmingResources(User $user)
    {
        return FarmingResource::factory()->for($user)->createMany([
            // Fertilizer
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

            // Pesticide
            [
                'name' => 'Palaas',
                'type' => FarmingResourceType::Pesticide,
                'quantity_unit' => QuantityUnit::Bottle,
                'rate' => random_int(3000, 6000),
            ],

            // Seed
            [
                'name' => 'Bijj',
                'type' => FarmingResourceType::Seed,
                'quantity_unit' => QuantityUnit::Sack,
                'rate' => random_int(3000, 6000),
            ],

            // Implement
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
            [
                'name' => 'Cultivator',
                'type' => FarmingResourceType::Implement,
                'quantity_unit' => QuantityUnit::Acre,
                'rate' => random_int(3000, 6000),
            ],
            [
                'name' => 'Banna',
                'type' => FarmingResourceType::Implement,
                'quantity_unit' => QuantityUnit::Acre,
                'rate' => random_int(3000, 6000),
            ],
        ]);
    }
}
