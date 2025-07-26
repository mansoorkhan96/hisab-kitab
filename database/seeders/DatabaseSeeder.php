<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Enums\FarmingResourceType;
use App\Enums\QuantityUnit;
use App\Models\Calculation;
use App\Models\CropSeason;
use App\Models\FarmingResource;
use App\Models\Ledger;
use App\Models\Loan;
use App\Models\Tractor;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public $user;

    public Tractor $tractorA;

    public Tractor $tractorB;

    public $farmingResources;

    public $cropSeason;

    public function run(): void
    {
        $this->user = User::factory()->admin()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->tractorA = Tractor::factory()
            ->for(User::factory()->driver()->create(['name' => 'Driver A']))
            ->create(['title' => 'Tractor A']);

        $this->tractorB = Tractor::factory()
            ->for(User::factory()->driver()->create(['name' => 'Driver B']))
            ->create(['title' => 'Tractor B']);

        $this->farmingResources = $this->seedFarmingResources($this->user);

        $this->cropSeason = CropSeason::factory()
            ->for($this->user)
            ->recycle($this->user)
            ->create([
                'title' => 'Season-'.now()->year,
                'is_current' => true,
            ]);

        $this->seedAshrafRind();
        $this->seedSadamLighari();
    }

    public function seedSadamLighari()
    {
        $farmer = User::factory()
            ->farmer()
            ->create(['name' => 'Sadam Lighari']);

        collect([
            ['farming_resource' => 'DAP', 'quantity' => 4, 'rate' => 14_000],
            ['farming_resource' => 'Urea', 'quantity' => 30, 'rate' => 3200],
            ['farming_resource' => 'Sanga', 'quantity' => 9, 'rate' => 1500],
            ['farming_resource' => 'Lambda', 'quantity' => 4, 'rate' => 1000],

            ['farming_resource' => 'Bijj', 'quantity' => 7, 'rate' => 11_000],
            ['farming_resource' => 'Kean', 'quantity' => 4, 'rate' => 2000],
            ['farming_resource' => 'Gobil', 'quantity' => 20, 'rate' => 2500],
            ['farming_resource' => 'Banna', 'quantity' => 1, 'rate' => 7500],
        ])->each(
            fn (array $ledger) => Ledger::factory()
                ->for($this->cropSeason)
                ->for($farmer)
                ->for($this->farmingResources->where('title', $ledger['farming_resource'])->first())
                ->create(['quantity' => $ledger['quantity'], 'rate' => $ledger['rate']])
        );

        collect([
            ['purpose' => 'Wheat 12 Borion 60 Kg', 'amount' => 131_920],
            ['purpose' => 'Bijj 1 Bori', 'amount' => 11_000],
            ['purpose' => 'Derran ji mazoori', 'amount' => 11_500],
            ['purpose' => 'Zameendar watan khanyal', 'amount' => 2500],
        ])->each(
            fn (array $loan) => Loan::factory()
                ->for($farmer)
                ->create($loan)
        );

        $calculation = Calculation::factory()
            ->for($farmer)
            ->for($this->cropSeason)
            ->create([
                'kudhi_in_kgs' => 1,
                'wheat_rate' => 9_700,
                'wheat_straw_rate' => 458.1673306773,
            ]);

        $calculation->threshings()->create([
            'tractor_id' => $this->tractorA->id,
            'total_wheat_sacks' => 100,
        ]);

        $calculation->threshings()->create([
            'tractor_id' => $this->tractorB->id,
            'total_wheat_sacks' => 25.5,
        ]);
    }

    public function seedAshrafRind()
    {
        $ashraf = User::factory()
            ->farmer()
            ->create(['name' => 'Ashraf Rind']);

        collect([
            ['farming_resource' => 'DAP', 'quantity' => 4.00, 'rate' => 9000.00],
            ['farming_resource' => 'Urea', 'quantity' => 21.00, 'rate' => 3000.00],
            ['farming_resource' => 'Palaas', 'quantity' => 5, 'rate' => 1800.00],
            ['farming_resource' => 'Bijj', 'quantity' => 10.50, 'rate' => 6000.00],
            ['farming_resource' => 'Kean', 'quantity' => 8.00, 'rate' => 2700.00],
            ['farming_resource' => 'Banna', 'quantity' => 8.00, 'rate' => 500.00],
            ['farming_resource' => 'Cultivator', 'quantity' => 8.00, 'rate' => 2700.00],
        ])->each(
            fn (array $ledger) => Ledger::factory()
                ->for($this->cropSeason)
                ->for($ashraf)
                ->for($this->farmingResources->where('title', $ledger['farming_resource'])->first())
                ->create(['quantity' => $ledger['quantity'], 'rate' => $ledger['rate']])
        );

        collect([
            ['purpose' => 'Laab 7 borion', 'amount' => 37_800],
            ['purpose' => 'Derran ji mazoori', 'amount' => 19_000],
            ['purpose' => '4 Borion Wheat', 'amount' => 21_000],
        ])->each(
            fn (array $loan) => Loan::factory()
                ->for($ashraf)
                ->create($loan)
        );

        $calculation = Calculation::factory()
            ->for($ashraf)
            ->for($this->cropSeason)
            ->create([
                'kudhi_in_kgs' => 0,
                'wheat_rate' => 5250,
                'wheat_straw_rate' => 310,
            ]);

        $calculation->threshings()->create([
            'tractor_id' => $this->tractorB->id,
            'total_wheat_sacks' => 70,
        ]);
    }

    public function seedFarmingResources(User $user)
    {
        return FarmingResource::factory()->for($user)->createMany([
            // Fertilizer
            [
                'title' => 'DAP',
                'type' => FarmingResourceType::Fertilizer,
                'quantity_unit' => QuantityUnit::Sack,
                'rate' => random_int(3000, 6000),
            ],
            [
                'title' => 'Urea',
                'type' => FarmingResourceType::Fertilizer,
                'quantity_unit' => QuantityUnit::Sack,
                'rate' => random_int(3000, 6000),
            ],

            // Pesticide
            [
                'title' => 'Palaas',
                'type' => FarmingResourceType::Pesticide,
                'quantity_unit' => QuantityUnit::Bottle,
                'rate' => random_int(3000, 6000),
            ],
            [
                'title' => 'Sanga',
                'type' => FarmingResourceType::Pesticide,
                'quantity_unit' => QuantityUnit::Bottle,
                'rate' => random_int(3000, 6000),
            ],
            [
                'title' => 'Lambda',
                'type' => FarmingResourceType::Pesticide,
                'quantity_unit' => QuantityUnit::Bottle,
                'rate' => random_int(3000, 6000),
            ],

            // Seed
            [
                'title' => 'Bijj',
                'type' => FarmingResourceType::Seed,
                'quantity_unit' => QuantityUnit::Sack,
                'rate' => random_int(3000, 6000),
            ],

            // Implement
            [
                'title' => 'Raja',
                'type' => FarmingResourceType::Implement,
                'quantity_unit' => QuantityUnit::Hour,
                'rate' => random_int(3000, 6000),
            ],
            [
                'title' => 'Kean',
                'type' => FarmingResourceType::Implement,
                'quantity_unit' => QuantityUnit::Hour,
                'rate' => random_int(3000, 6000),
            ],
            [
                'title' => 'Cultivator',
                'type' => FarmingResourceType::Implement,
                'quantity_unit' => QuantityUnit::Acre,
                'rate' => random_int(3000, 6000),
            ],
            [
                'title' => 'Gobil',
                'type' => FarmingResourceType::Implement,
                'quantity_unit' => QuantityUnit::Acre,
                'rate' => random_int(3000, 6000),
            ],
            [
                'title' => 'Banna',
                'type' => FarmingResourceType::Implement,
                'quantity_unit' => QuantityUnit::Acre,
                'rate' => random_int(3000, 6000),
            ],
        ]);
    }
}
