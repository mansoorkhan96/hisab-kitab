<?php

use App\Models\Calculation;
use App\Models\CropSeason;
use App\Models\FarmingResource;
use App\Models\Ledger;
use App\Models\User;
use App\ValueObjects\AbastractCalculationReport;

it('sums fertilizer and pesticide into one bucket and implement with seed into another', function () {
    $admin = actingAsAdmin();
    $team = $admin->team;
    $season = makeCurrentSeason($team);
    $farmer = User::factory()->farmer()->create(['team_id' => $team->id]);

    $calculation = Calculation::factory()->wheat()->create([
        'team_id' => $team->id,
        'user_id' => $farmer->id,
        'crop_season_id' => $season->id,
    ]);

    $fertilizer = FarmingResource::factory()->fertilizer()->create(['team_id' => $team->id]);
    $pesticide = FarmingResource::factory()->pesticide()->create(['team_id' => $team->id]);
    $seed = FarmingResource::factory()->seed()->create(['team_id' => $team->id]);
    $implement = FarmingResource::factory()->implement()->create(['team_id' => $team->id]);

    Ledger::factory()->create([
        'user_id' => $farmer->id,
        'crop_season_id' => $season->id,
        'farming_resource_id' => $fertilizer->id,
        'quantity' => 2,
        'rate' => 1_000,
    ]);
    Ledger::factory()->create([
        'user_id' => $farmer->id,
        'crop_season_id' => $season->id,
        'farming_resource_id' => $pesticide->id,
        'quantity' => 1,
        'rate' => 500,
    ]);
    Ledger::factory()->create([
        'user_id' => $farmer->id,
        'crop_season_id' => $season->id,
        'farming_resource_id' => $seed->id,
        'quantity' => 1,
        'rate' => 800,
    ]);
    Ledger::factory()->create([
        'user_id' => $farmer->id,
        'crop_season_id' => $season->id,
        'farming_resource_id' => $implement->id,
        'quantity' => 3,
        'rate' => 400,
    ]);

    $expenses = AbastractCalculationReport::calculateExpenses($calculation);

    expect($expenses['fertilizerExpenseAmount'])->toEqual(2_500)
        ->and($expenses['implementAndSeedExpenseAmount'])->toEqual(2_000);
});

it('excludes ledgers for a different user or crop season', function () {
    $admin = actingAsAdmin();
    $team = $admin->team;
    $season = makeCurrentSeason($team);
    $otherSeason = CropSeason::factory()->create(['team_id' => $team->id]);
    $farmer = User::factory()->farmer()->create(['team_id' => $team->id]);
    $otherFarmer = User::factory()->farmer()->create(['team_id' => $team->id]);

    $calculation = Calculation::factory()->wheat()->create([
        'team_id' => $team->id,
        'user_id' => $farmer->id,
        'crop_season_id' => $season->id,
    ]);

    $fertilizer = FarmingResource::factory()->fertilizer()->create(['team_id' => $team->id]);
    $seed = FarmingResource::factory()->seed()->create(['team_id' => $team->id]);

    Ledger::factory()->create([
        'user_id' => $farmer->id,
        'crop_season_id' => $season->id,
        'farming_resource_id' => $fertilizer->id,
        'quantity' => 1,
        'rate' => 1_000,
    ]);
    Ledger::factory()->create([
        'user_id' => $otherFarmer->id,
        'crop_season_id' => $season->id,
        'farming_resource_id' => $fertilizer->id,
        'quantity' => 1,
        'rate' => 9_999,
    ]);
    Ledger::factory()->create([
        'user_id' => $farmer->id,
        'crop_season_id' => $otherSeason->id,
        'farming_resource_id' => $seed->id,
        'quantity' => 1,
        'rate' => 8_888,
    ]);
    Ledger::factory()->create([
        'user_id' => $farmer->id,
        'crop_season_id' => $season->id,
        'farming_resource_id' => $seed->id,
        'quantity' => 1,
        'rate' => 400,
    ]);

    $expenses = AbastractCalculationReport::calculateExpenses($calculation);

    expect($expenses['fertilizerExpenseAmount'])->toEqual(1_000)
        ->and($expenses['implementAndSeedExpenseAmount'])->toEqual(400);
});
