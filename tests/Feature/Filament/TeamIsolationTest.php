<?php

use App\Filament\Resources\Calculations\Pages\EditCalculation;
use App\Filament\Resources\Calculations\Pages\ListCalculations;
use App\Models\Calculation;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use function Pest\Livewire\livewire;

beforeEach(fn () => Filament::setCurrentPanel('admin'));

it('does not list calculations belonging to another team', function () {
    $adminA = actingAsAdmin();
    $teamA = $adminA->team;
    $seasonA = makeCurrentSeason($teamA);
    $farmerA = User::factory()->farmer()->create(['team_id' => $teamA->id]);

    $ownCalculation = Calculation::factory()->wheat()->create([
        'team_id' => $teamA->id,
        'user_id' => $farmerA->id,
        'crop_season_id' => $seasonA->id,
    ]);

    $teamB = Team::factory()->create();
    $adminB = actingAsAdmin($teamB);
    $seasonB = makeCurrentSeason($teamB);
    $farmerB = User::factory()->farmer()->create(['team_id' => $teamB->id]);

    $foreignCalculation = Calculation::factory()->wheat()->create([
        'team_id' => $teamB->id,
        'user_id' => $farmerB->id,
        'crop_season_id' => $seasonB->id,
    ]);

    // Resume as team A so the global team scope applies to the list page.
    test()->actingAs($adminA);

    livewire(ListCalculations::class)
        ->assertSuccessful()
        ->assertCanSeeTableRecords([$ownCalculation])
        ->assertCanNotSeeTableRecords([$foreignCalculation]);
});

it('forbids editing a calculation that belongs to another team', function () {
    $adminA = actingAsAdmin();

    $teamB = Team::factory()->create();
    actingAsAdmin($teamB);
    $seasonB = makeCurrentSeason($teamB);
    $farmerB = User::factory()->farmer()->create(['team_id' => $teamB->id]);

    $foreignCalculation = Calculation::factory()->wheat()->create([
        'team_id' => $teamB->id,
        'user_id' => $farmerB->id,
        'crop_season_id' => $seasonB->id,
    ]);

    test()->actingAs($adminA);

    expect(fn () => livewire(EditCalculation::class, [
        'record' => $foreignCalculation->getRouteKey(),
    ]))->toThrow(ModelNotFoundException::class);
});
