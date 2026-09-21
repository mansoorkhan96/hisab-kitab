<?php

use App\Filament\Resources\CottonPickingRounds\Pages\EditCottonPickingRound;
use App\Filament\Resources\CottonPickingRounds\RelationManagers\CottonPickingDailiesRelationManager;
use App\Models\CottonPickingDaily;
use App\Models\CottonPickingRound;
use App\Models\Labourer;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Repeater;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Livewire\livewire;

beforeEach(function () {
    Filament::setCurrentPanel('admin');
    Repeater::fake();
});

/**
 * @return array{round: CottonPickingRound, labourer: Labourer}
 */
function cottonPickingFixtures(array $seasonOverrides = []): array
{
    $admin = actingAsAdmin();
    $team = $admin->team;
    $season = makeCurrentSeason($team, $seasonOverrides);
    $farmer = User::factory()->farmer()->create(['team_id' => $team->id]);

    $round = CottonPickingRound::factory()->create([
        'team_id' => $team->id,
        'crop_season_id' => $season->id,
        'user_id' => $farmer->id,
    ]);

    $labourer = Labourer::factory()->create([
        'team_id' => $team->id,
        'name' => 'Ali',
    ]);

    return compact('round', 'labourer');
}

it('mounts the cotton picking dailies relation manager on the edit round page', function () {
    ['round' => $round] = cottonPickingFixtures();

    livewire(CottonPickingDailiesRelationManager::class, [
        'ownerRecord' => $round,
        'pageClass' => EditCottonPickingRound::class,
    ])
        ->assertSuccessful();
});

it('creates a daily from a plus expression and persists the evaluated total', function () {
    ['round' => $round, 'labourer' => $labourer] = cottonPickingFixtures();
    $pickingDate = now()->toDateString();

    // kgs_picked_raw is dehydrated; afterStateUpdated evals it into kgs_picked.
    // Tests set the evaluated total the action persists (same as a blur on "2+3").
    livewire(CottonPickingDailiesRelationManager::class, [
        'ownerRecord' => $round,
        'pageClass' => EditCottonPickingRound::class,
    ])
        ->callAction(TestAction::make('create')->table(), data: [
            'picking_date' => $pickingDate,
            'cotton_picking_daily' => [
                [
                    'labourer_id' => $labourer->id,
                    'name' => $labourer->name,
                    'kgs_picked_raw' => '2+3',
                    'kgs_picked' => 5,
                ],
            ],
        ])
        ->assertHasNoFormErrors();

    $daily = CottonPickingDaily::query()
        ->where('cotton_picking_round_id', $round->id)
        ->where('labourer_id', $labourer->id)
        ->first();

    expect($daily)->not->toBeNull()
        ->and($daily->picking_date->toDateString())->toBe($pickingDate)
        ->and((float) $daily->kgs_picked)->toBe(5.0);
});

it('rejects invalid kgs picked expressions', function () {
    ['round' => $round, 'labourer' => $labourer] = cottonPickingFixtures();

    livewire(CottonPickingDailiesRelationManager::class, [
        'ownerRecord' => $round,
        'pageClass' => EditCottonPickingRound::class,
    ])
        ->callAction(TestAction::make('create')->table(), data: [
            'picking_date' => now()->toDateString(),
            'cotton_picking_daily' => [
                [
                    'labourer_id' => $labourer->id,
                    'name' => $labourer->name,
                    'kgs_picked_raw' => '2*3',
                    'kgs_picked' => 0,
                ],
            ],
        ])
        ->assertHasFormErrors(['cotton_picking_daily.0.kgs_picked_raw']);

    assertDatabaseCount(CottonPickingDaily::class, 0);
});

it('rejects a duplicate picking date for the same round', function () {
    ['round' => $round, 'labourer' => $labourer] = cottonPickingFixtures();
    $pickingDate = now()->toDateString();

    CottonPickingDaily::factory()->create([
        'cotton_picking_round_id' => $round->id,
        'labourer_id' => $labourer->id,
        'picking_date' => $pickingDate,
        'kgs_picked' => 10,
    ]);

    livewire(CottonPickingDailiesRelationManager::class, [
        'ownerRecord' => $round,
        'pageClass' => EditCottonPickingRound::class,
    ])
        ->callAction(TestAction::make('create')->table(), data: [
            'picking_date' => $pickingDate,
            'cotton_picking_daily' => [
                [
                    'labourer_id' => $labourer->id,
                    'name' => $labourer->name,
                    'kgs_picked_raw' => '1',
                    'kgs_picked' => 1,
                ],
            ],
        ])
        ->assertHasFormErrors(['picking_date']);

    assertDatabaseCount(CottonPickingDaily::class, 1);
});

it('halts create when all labourers have zero kgs picked', function () {
    ['round' => $round, 'labourer' => $labourer] = cottonPickingFixtures();

    livewire(CottonPickingDailiesRelationManager::class, [
        'ownerRecord' => $round,
        'pageClass' => EditCottonPickingRound::class,
    ])
        ->callAction(TestAction::make('create')->table(), data: [
            'picking_date' => now()->toDateString(),
            'cotton_picking_daily' => [
                [
                    'labourer_id' => $labourer->id,
                    'name' => $labourer->name,
                    'kgs_picked_raw' => '0',
                    'kgs_picked' => 0,
                ],
            ],
        ])
        ->assertNotified()
        ->assertActionHalted(TestAction::make('create')->table());

    assertDatabaseCount(CottonPickingDaily::class, 0);
});
