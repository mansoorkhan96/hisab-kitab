<?php

use App\Filament\Components\CottonCalculationInfolist;
use App\Models\Calculation;
use App\Models\CottonPickingDaily;
use App\Models\CottonPickingRound;
use App\Models\Labourer;
use App\Models\LoanPayment;
use App\Models\User;
use App\ValueObjects\CottonCropCalculationReport;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Livewire\livewire;

beforeEach(fn () => Filament::setCurrentPanel('admin'));

it('mounts the cotton calculation infolist with farmer revenue', function () {
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
        'kgs_picked' => 400,
        'picking_date' => '2026-09-01',
    ]);

    $calculation = $calculation->fresh(['cropSeason', 'user']);
    $report = CottonCropCalculationReport::make($calculation);

    livewire(CottonCalculationInfolist::class, ['calculation' => $calculation])
        ->assertSuccessful()
        ->assertSee('Farmer Revenue')
        ->assertSee('Subtract Loan')
        ->assertSchemaStateSet([
            'farmerRevenue' => $report->farmerRevenue,
            'grossRevenue' => $report->grossRevenue,
            'landlordRevenue' => $report->landlordRevenue,
        ], 'infolist');
});

it('subtracts a loan payment when the cotton farmer has profit', function () {
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
        'kgs_picked' => 400,
        'picking_date' => '2026-09-01',
    ]);

    livewire(CottonCalculationInfolist::class, [
        'calculation' => $calculation->fresh(['cropSeason', 'user']),
    ])
        ->callAction(
            TestAction::make('subtract_loan')->schemaComponent('loan_payment', schema: 'infolist'),
            data: [
                'amount' => 750,
                'notes' => 'Cotton loan subtract',
            ],
        )
        ->assertHasNoFormErrors();

    assertDatabaseHas(LoanPayment::class, [
        'calculation_id' => $calculation->id,
        'user_id' => $farmer->id,
        'amount' => 750,
        'notes' => 'Cotton loan subtract',
    ]);
});

it('disables subtract loan when the cotton farmer is in loss', function () {
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
        'kamdari' => 0,
    ]);

    livewire(CottonCalculationInfolist::class, [
        'calculation' => $calculation->fresh(['cropSeason', 'user']),
    ])
        ->assertInfolistActionDisabled('loan_payment', 'subtract_loan');
});

it('rejects cotton loan amounts above farmer revenue', function () {
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
        'kgs_picked' => 400,
        'picking_date' => '2026-09-01',
    ]);

    $calculation = $calculation->fresh(['cropSeason', 'user']);
    $report = CottonCropCalculationReport::make($calculation);

    livewire(CottonCalculationInfolist::class, ['calculation' => $calculation])
        ->callInfolistAction('loan_payment', 'subtract_loan', data: [
            'amount' => $report->farmerRevenue + 1,
            'notes' => 'Over max',
        ])
        ->assertHasFormErrors(['amount']);
});

it('deletes a loan payment from the cotton infolist', function () {
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
        'kgs_picked' => 400,
        'picking_date' => '2026-09-01',
    ]);

    $payment = LoanPayment::factory()->create([
        'user_id' => $farmer->id,
        'calculation_id' => $calculation->id,
        'amount' => 200,
        'notes' => 'Remove me',
    ]);

    livewire(CottonCalculationInfolist::class, [
        'calculation' => $calculation->fresh(['cropSeason', 'user']),
    ])
        ->callInfolistAction('loan_payment.0.amount', 'delete');

    assertDatabaseMissing(LoanPayment::class, [
        'id' => $payment->id,
    ]);
});
