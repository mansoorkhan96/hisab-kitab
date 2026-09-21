<?php

use App\Filament\Components\WheatCalculationInfolist;
use App\Models\Calculation;
use App\Models\LoanPayment;
use App\Models\Threshing;
use App\Models\Tractor;
use App\Models\User;
use App\ValueObjects\WheatCropCalculationReport;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Livewire\livewire;

beforeEach(fn () => Filament::setCurrentPanel('admin'));

it('mounts the wheat calculation infolist with farmer revenue', function () {
    $admin = actingAsAdmin();
    $team = $admin->team;
    $season = makeCurrentSeason($team, ['wheat_rate' => 4_000]);
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

    $calculation = $calculation->fresh(['cropSeason', 'user']);
    $report = WheatCropCalculationReport::make($calculation);

    livewire(WheatCalculationInfolist::class, ['calculation' => $calculation])
        ->assertSuccessful()
        ->assertSee('Farmer Revenue')
        ->assertSee('Subtract Loan')
        ->assertSchemaStateSet([
            'farmerRevenue' => $report->farmerRevenue,
            'grossRevenue' => $report->grossRevenue,
            'landlordRevenue' => $report->landlordRevenue,
        ], 'infolist');
});

it('subtracts a loan payment when the farmer has profit', function () {
    $admin = actingAsAdmin();
    $team = $admin->team;
    $season = makeCurrentSeason($team, ['wheat_rate' => 4_000]);
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

    livewire(WheatCalculationInfolist::class, [
        'calculation' => $calculation->fresh(['cropSeason', 'user']),
    ])
        ->callAction(
            TestAction::make('subtract_loan')->schemaComponent('loan_payment', schema: 'infolist'),
            data: [
                'amount' => 500,
                'notes' => 'Wheat loan subtract',
            ],
        )
        ->assertHasNoFormErrors();

    assertDatabaseHas(LoanPayment::class, [
        'calculation_id' => $calculation->id,
        'user_id' => $farmer->id,
        'amount' => 500,
        'notes' => 'Wheat loan subtract',
    ]);
});

it('disables subtract loan when the farmer is in loss', function () {
    $admin = actingAsAdmin();
    $team = $admin->team;
    $season = makeCurrentSeason($team, ['wheat_rate' => 4_000]);
    $farmer = User::factory()->farmer()->create(['team_id' => $team->id]);

    $calculation = Calculation::factory()->wheat()->create([
        'team_id' => $team->id,
        'user_id' => $farmer->id,
        'crop_season_id' => $season->id,
        'kudhi_in_kgs' => 0,
        'kamdari' => 0,
        'wheat_straw_rate' => 0,
    ]);

    livewire(WheatCalculationInfolist::class, [
        'calculation' => $calculation->fresh(['cropSeason', 'user']),
    ])
        ->assertInfolistActionDisabled('loan_payment', 'subtract_loan');
});

it('rejects loan amounts above farmer revenue', function () {
    $admin = actingAsAdmin();
    $team = $admin->team;
    $season = makeCurrentSeason($team, ['wheat_rate' => 4_000]);
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

    $calculation = $calculation->fresh(['cropSeason', 'user']);
    $report = WheatCropCalculationReport::make($calculation);

    livewire(WheatCalculationInfolist::class, ['calculation' => $calculation])
        ->callInfolistAction('loan_payment', 'subtract_loan', data: [
            'amount' => $report->farmerRevenue + 1,
            'notes' => 'Over max',
        ])
        ->assertHasFormErrors(['amount']);
});

it('deletes a loan payment from the wheat infolist', function () {
    $admin = actingAsAdmin();
    $team = $admin->team;
    $season = makeCurrentSeason($team, ['wheat_rate' => 4_000]);
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

    $payment = LoanPayment::factory()->create([
        'user_id' => $farmer->id,
        'calculation_id' => $calculation->id,
        'amount' => 200,
        'notes' => 'Remove me',
    ]);

    livewire(WheatCalculationInfolist::class, [
        'calculation' => $calculation->fresh(['cropSeason', 'user']),
    ])
        ->callInfolistAction('loan_payment.0.amount', 'delete');

    assertDatabaseMissing(LoanPayment::class, [
        'id' => $payment->id,
    ]);
});
