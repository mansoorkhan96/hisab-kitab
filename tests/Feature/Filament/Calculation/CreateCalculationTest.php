<?php

use App\Enums\CropType;
use App\Filament\Resources\Calculations\Pages\CreateCalculation;
use App\Models\Calculation;
use App\Models\User;
use Filament\Facades\Filament;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;

beforeEach(function () {
    Filament::setCurrentPanel('admin');
});

it('creates a calculation for the admin team', function () {
    $admin = actingAsAdmin();
    $team = $admin->team;
    $season = makeCurrentSeason($team, [
        'wheat_rate' => 4_000,
    ]);
    $farmer = User::factory()->farmer()->create(['team_id' => $team->id]);

    livewire(CreateCalculation::class)
        ->fillForm([
            'crop_season_id' => $season->id,
            'user_id' => $farmer->id,
            'crop_type' => CropType::Wheat->value,
            'kudhi_in_kgs' => 50,
            'kamdari' => 25,
            'wheat_straw_rate' => 100,
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified()
        ->assertRedirect();

    assertDatabaseHas(Calculation::class, [
        'team_id' => $team->id,
        'crop_season_id' => $season->id,
        'user_id' => $farmer->id,
        'crop_type' => CropType::Wheat->value,
        'kudhi_in_kgs' => 50,
        'kamdari' => 25,
        'wheat_straw_rate' => 100,
    ]);
});

it('validates required fields when creating a calculation', function () {
    $admin = actingAsAdmin();
    makeCurrentSeason($admin->team, [
        'wheat_rate' => 4_000,
    ]);

    livewire(CreateCalculation::class)
        ->fillForm([
            'crop_season_id' => null,
            'user_id' => null,
            'crop_type' => null,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'crop_season_id' => 'required',
            'user_id' => 'required',
            'crop_type' => 'required',
        ])
        ->assertNotNotified();
});
