<?php

use App\Filament\Resources\Calculations\Pages\EditCalculation;
use App\Filament\Resources\Calculations\RelationManagers\ThreshingsRelationManager;
use App\Models\Calculation;
use App\Models\Threshing;
use App\Models\Tractor;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;

beforeEach(fn () => Filament::setCurrentPanel('admin'));

it('creates a threshing on a calculation and aligns charges with season wheat rate', function () {
    $admin = actingAsAdmin();
    $team = $admin->team;
    $season = makeCurrentSeason($team, ['wheat_rate' => 4_000]);
    $farmer = User::factory()->farmer()->create(['team_id' => $team->id]);

    $calculation = Calculation::factory()->wheat()->create([
        'team_id' => $team->id,
        'user_id' => $farmer->id,
        'crop_season_id' => $season->id,
    ]);

    $tractor = Tractor::factory()->create([
        'team_id' => $team->id,
        'user_id' => User::factory()->driver()->create(['team_id' => $team->id])->id,
    ]);

    livewire(ThreshingsRelationManager::class, [
        'ownerRecord' => $calculation,
        'pageClass' => EditCalculation::class,
    ])
        ->assertSuccessful()
        ->callAction(TestAction::make('create')->table(), data: [
            'tractor_id' => $tractor->id,
            'total_wheat_sacks' => 10,
        ])
        ->assertHasNoFormErrors();

    assertDatabaseHas(Threshing::class, [
        'calculation_id' => $calculation->id,
        'tractor_id' => $tractor->id,
        'total_wheat_sacks' => 10,
    ]);

    $threshing = Threshing::query()
        ->where('calculation_id', $calculation->id)
        ->where('tractor_id', $tractor->id)
        ->first();

    // Generated column: (total_wheat_sacks * 10) / 100
    expect((float) $threshing->threshing_charges_in_sacks)->toBe(1.0);

    $amount = (float) $threshing->threshing_charges_in_sacks * (float) $season->wheat_rate;

    expect($amount)->toBe(4_000.0);
});
