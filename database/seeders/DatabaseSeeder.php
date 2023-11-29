<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\CropSeason;
use App\Models\Farmer;
use App\Models\FarmingResource;
use App\Models\Ledger;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = \App\Models\User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call(FarmingResourcesTableSeeder::class);

        $cropSeason = CropSeason::factory()->for($user)->recycle($user)->create();

        $farmers = Farmer::factory(5)->for($user)->create();

        Ledger::factory(100)->recycle($cropSeason)->create([
            'farmer_id' => fn () => $farmers->random()->id,
            'farming_resource_id' => fn () => FarmingResource::inRandomOrder()->first()->id,
        ]);
    }
}
