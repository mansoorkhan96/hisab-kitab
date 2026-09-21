<?php

use App\Models\Calculation;
use App\Models\CottonPickingDaily;
use App\Models\CottonPickingRound;
use App\Models\FarmingResource;
use App\Models\Labourer;
use App\Models\Ledger;
use App\Models\LoanPayment;
use App\Models\Threshing;
use App\Models\Tractor;
use App\Models\User;
use App\ValueObjects\CottonCropCalculationReport;
use App\ValueObjects\WheatCropCalculationReport;

it('persists wheat calculation revenue columns from the wheat report on save', function () {
    $admin = actingAsAdmin();
    $team = $admin->team;
    $season = makeCurrentSeason($team, [
        'wheat_rate' => 4_000,
    ]);
    $farmer = User::factory()->farmer()->create(['team_id' => $team->id]);

    $calculation = Calculation::factory()->wheat()->create([
        'team_id' => $team->id,
        'user_id' => $farmer->id,
        'crop_season_id' => $season->id,
        'kudhi_in_kgs' => 50,
        'kamdari' => 50,
        'wheat_straw_rate' => 100,
    ]);

    $tractor = Tractor::factory()->create([
        'team_id' => $team->id,
        'user_id' => User::factory()->driver()->create(['team_id' => $team->id])->id,
    ]);

    Threshing::factory()->create([
        'calculation_id' => $calculation->id,
        'tractor_id' => $tractor->id,
        'total_wheat_sacks' => 10,
    ]);

    $fertilizer = FarmingResource::factory()->fertilizer()->create(['team_id' => $team->id]);
    $pesticide = FarmingResource::factory()->pesticide()->create(['team_id' => $team->id]);
    $seed = FarmingResource::factory()->seed()->create(['team_id' => $team->id]);
    $implement = FarmingResource::factory()->implement()->create(['team_id' => $team->id]);

    Ledger::factory()->create([
        'user_id' => $farmer->id,
        'crop_season_id' => $season->id,
        'farming_resource_id' => $fertilizer->id,
        'quantity' => 1,
        'rate' => 1_600,
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
        'rate' => 400,
    ]);
    Ledger::factory()->create([
        'user_id' => $farmer->id,
        'crop_season_id' => $season->id,
        'farming_resource_id' => $implement->id,
        'quantity' => 1,
        'rate' => 800,
    ]);

    LoanPayment::factory()->create([
        'user_id' => $farmer->id,
        'calculation_id' => $calculation->id,
        'amount' => 200,
    ]);

    $calculation->save();

    $report = WheatCropCalculationReport::make($calculation->fresh(['cropSeason', 'user']));
    $calculation->refresh();

    expect((float) $calculation->landlord_revenue)->toBe($report->landlordRevenue)
        ->and((float) $calculation->landlord_net_income)->toBe($report->landlordRevenue + $report->machineAmount)
        ->and((float) $calculation->farmer_gross_revenue)->toBe($report->farmerGrossRevenue)
        ->and((float) $calculation->farmer_revenue)->toBe($report->farmerRevenue);
});

it('persists cotton calculation revenue columns from the cotton report on save', function () {
    $admin = actingAsAdmin();
    $team = $admin->team;
    $season = makeCurrentSeason($team, [
        'cotton_rate_per_kg' => 8_000,
        'cotton_labour_rate_per_kg' => 25,
    ]);
    $farmer = User::factory()->farmer()->create(['team_id' => $team->id]);

    $calculation = Calculation::factory()->cotton()->create([
        'team_id' => $team->id,
        'user_id' => $farmer->id,
        'crop_season_id' => $season->id,
        'kamdari' => 1_000,
    ]);

    $round = CottonPickingRound::factory()->create([
        'team_id' => $team->id,
        'crop_season_id' => $season->id,
        'user_id' => $farmer->id,
    ]);

    $labourer = Labourer::factory()->create(['team_id' => $team->id]);

    CottonPickingDaily::factory()->create([
        'cotton_picking_round_id' => $round->id,
        'labourer_id' => $labourer->id,
        'kgs_picked' => 250,
        'picking_date' => '2026-09-01',
    ]);
    CottonPickingDaily::factory()->create([
        'cotton_picking_round_id' => $round->id,
        'labourer_id' => $labourer->id,
        'kgs_picked' => 150,
        'picking_date' => '2026-09-02',
    ]);

    $fertilizer = FarmingResource::factory()->fertilizer()->create(['team_id' => $team->id]);
    $pesticide = FarmingResource::factory()->pesticide()->create(['team_id' => $team->id]);
    $seed = FarmingResource::factory()->seed()->create(['team_id' => $team->id]);
    $implement = FarmingResource::factory()->implement()->create(['team_id' => $team->id]);

    Ledger::factory()->create([
        'user_id' => $farmer->id,
        'crop_season_id' => $season->id,
        'farming_resource_id' => $fertilizer->id,
        'quantity' => 1,
        'rate' => 1_600,
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
        'rate' => 400,
    ]);
    Ledger::factory()->create([
        'user_id' => $farmer->id,
        'crop_season_id' => $season->id,
        'farming_resource_id' => $implement->id,
        'quantity' => 1,
        'rate' => 800,
    ]);

    LoanPayment::factory()->create([
        'user_id' => $farmer->id,
        'calculation_id' => $calculation->id,
        'amount' => 200,
    ]);

    $calculation->save();

    $report = CottonCropCalculationReport::make($calculation->fresh(['cropSeason', 'user']));
    $calculation->refresh();

    expect((float) $calculation->landlord_revenue)->toBe($report->landlordRevenue)
        ->and((float) $calculation->landlord_net_income)->toBe($report->landlordRevenue + $report->machineAmount)
        ->and((float) $calculation->farmer_gross_revenue)->toBe($report->farmerGrossRevenue)
        ->and((float) $calculation->farmer_revenue)->toBe($report->farmerRevenue);
});
