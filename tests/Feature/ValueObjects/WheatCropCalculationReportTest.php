<?php

use App\Models\Calculation;
use App\Models\FarmingResource;
use App\Models\Ledger;
use App\Models\LoanPayment;
use App\Models\Threshing;
use App\Models\Tractor;
use App\Models\User;
use App\ValueObjects\WheatCropCalculationReport;
use Illuminate\Support\Number;

it('calculates wheat harvest, straw revenue, kudhi bonus, and loan-adjusted farmer share', function () {
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

    $report = WheatCropCalculationReport::make($calculation->fresh(['cropSeason', 'user']));

    // 10 sacks → 1000 kg; thresher 100 kg; kudhi 50; kamdari 50 → net 800 kg
    // grain = (800/100)*4000 = 32000; straw = (10*2.5)*100 = 2500; gross = 34500
    // fert 2100 → after 32400; machine 10800; after machine 21600; implement+seed 1200 → net 20400
    // shares 10200; kudhi bonus (50/100)*4000 = 2000; farmer gross 12200 − loan 200 = 12000
    expect($report->totalWheatSacks)->toBe('10 Borion')
        ->and($report->thresher)->toBe('1 Bori')
        ->and($report->remainingAfterThresher)->toBe('9 Borion')
        ->and($report->kudhi)->toBe('50 KGs')
        ->and($report->remainingAfterKudhi)->toBe('8 Borion, 50 KGs')
        ->and($report->kamdari)->toBe('50 KGs')
        ->and($report->remainingAfterKamdari)->toBe('8 Borion')
        ->and($report->sackAmount)->toBe(32_000.0)
        ->and($report->buhAmount)->toBe(2_500.0)
        ->and($report->grossRevenue)->toBe(34_500.0)
        ->and($report->fertilizerExpenseAmount)->toBe(2_100.0)
        ->and($report->remainingAfterFertilizerExpenseAmount)->toBe(Number::currency(32_400, 'PKR'))
        ->and($report->machineAmount)->toBe(10_800.0)
        ->and($report->remainingAfterMachineAmount)->toBe(Number::currency(21_600, 'PKR'))
        ->and($report->implementAndSeedExpenseAmount)->toBe(1_200.0)
        ->and($report->remainingAfterImplementAndSeedExpenseAmount)->toBe(Number::currency(20_400, 'PKR'))
        ->and($report->landlordRevenue)->toBe(10_200.0)
        ->and($report->farmerBaseRevenue)->toBe(10_200.0)
        ->and($report->farmerKudhiAmount)->toBe(2_000.0)
        ->and($report->farmerGrossRevenue)->toBe(12_200.0)
        ->and($report->loanPaymentsAmount)->toBe(200.0)
        ->and($report->farmerRevenue)->toBe(12_000.0);
});
