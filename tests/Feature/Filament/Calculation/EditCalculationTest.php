<?php

use App\Enums\CropType;
use App\Filament\Resources\Calculations\Pages\EditCalculation;
use App\Models\Calculation;
use App\Models\User;
use App\ValueObjects\WheatCropCalculationReport;
use Filament\Facades\Filament;

use function Pest\Livewire\livewire;

beforeEach(function () {
    Filament::setCurrentPanel('admin');
});

it('persists wheat kudhi kamdari and straw changes and recalculates revenue columns', function () {
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
        'kudhi_in_kgs' => 10,
        'kamdari' => 10,
        'wheat_straw_rate' => 50,
    ]);

    livewire(EditCalculation::class, ['record' => $calculation->getRouteKey()])
        ->fillForm([
            'kudhi_in_kgs' => 50,
            'kamdari' => 50,
            'wheat_straw_rate' => 100,
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified();

    $calculation->refresh();

    expect((float) $calculation->kudhi_in_kgs)->toBe(50.0)
        ->and((float) $calculation->kamdari)->toBe(50.0)
        ->and((float) $calculation->wheat_straw_rate)->toBe(100.0);

    $report = WheatCropCalculationReport::make($calculation->fresh(['cropSeason', 'user']));

    expect((float) $calculation->landlord_revenue)->toBe($report->landlordRevenue)
        ->and((float) $calculation->landlord_net_income)->toBe($report->landlordRevenue + $report->machineAmount)
        ->and((float) $calculation->farmer_gross_revenue)->toBe($report->farmerGrossRevenue)
        ->and((float) $calculation->farmer_revenue)->toBe($report->farmerRevenue);
});

it('hides kudhi and wheat straw fields when crop type is cotton', function () {
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
    ]);

    livewire(EditCalculation::class, ['record' => $calculation->getRouteKey()])
        ->assertFormFieldVisible('kudhi_in_kgs')
        ->assertFormFieldVisible('wheat_straw_rate')
        ->fillForm([
            'crop_type' => CropType::Cotton->value,
        ])
        ->assertFormFieldIsHidden('kudhi_in_kgs')
        ->assertFormFieldIsHidden('wheat_straw_rate');
});
