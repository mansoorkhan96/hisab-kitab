<?php

use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\RelationManagers\LedgersRelationManager;
use App\Models\FarmingResource;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;

use function Pest\Livewire\livewire;

beforeEach(fn () => Filament::setCurrentPanel('admin'));

it('prefills rate when a farming resource is selected', function () {
    $admin = actingAsAdmin();
    $team = $admin->team;
    makeCurrentSeason($team);
    $farmer = User::factory()->farmer()->create(['team_id' => $team->id]);

    $resource = FarmingResource::factory()->fertilizer()->create([
        'team_id' => $team->id,
        'rate' => 4_000,
    ]);

    livewire(LedgersRelationManager::class, [
        'ownerRecord' => $farmer,
        'pageClass' => EditUser::class,
    ])
        ->mountAction(TestAction::make('Add new')->table())
        ->fillForm([
            'farming_resource_id' => $resource->id,
        ])
        ->assertSchemaStateSet([
            'rate' => 4_000,
        ]);
});

it('requires tractor_id when the farming resource type is Implement', function () {
    $admin = actingAsAdmin();
    $team = $admin->team;
    $season = makeCurrentSeason($team);
    $farmer = User::factory()->farmer()->create(['team_id' => $team->id]);

    $implement = FarmingResource::factory()->implement()->create([
        'team_id' => $team->id,
        'rate' => 2_500,
    ]);

    livewire(LedgersRelationManager::class, [
        'ownerRecord' => $farmer,
        'pageClass' => EditUser::class,
    ])
        ->callAction(TestAction::make('Add new')->table(), data: [
            'crop_season_id' => $season->id,
            'farming_resource_id' => $implement->id,
            'quantity' => 1,
            'rate' => 2_500,
        ])
        ->assertHasFormErrors(['tractor_id']);
});
