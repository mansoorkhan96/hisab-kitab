<?php

use App\Models\CropSeason;
use App\Models\FarmingResource;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

it('hides crop seasons and farming resources belonging to another team', function () {
    $adminA = actingAsAdmin();
    $teamA = $adminA->team;
    $teamB = Team::factory()->create();

    $seasonA = makeCurrentSeason($teamA, ['title' => 'Team A Season']);
    $resourceA = FarmingResource::factory()->create(['team_id' => $teamA->id]);

    $seasonB = CropSeason::factory()->create([
        'team_id' => $teamB->id,
        'title' => 'Team B Season',
        'is_current' => false,
    ]);
    $resourceB = FarmingResource::factory()->create(['team_id' => $teamB->id]);

    expect(CropSeason::query()->pluck('id')->all())
        ->toContain($seasonA->id)
        ->not->toContain($seasonB->id)
        ->and(FarmingResource::query()->pluck('id')->all())
        ->toContain($resourceA->id)
        ->not->toContain($resourceB->id)
        ->and(CropSeason::withoutGlobalScopes()->find($seasonB->id))->not->toBeNull()
        ->and(FarmingResource::withoutGlobalScopes()->find($resourceB->id))->not->toBeNull();
});

it('fills team_id from the authenticated user when creating without one', function () {
    $admin = actingAsAdmin();

    $season = CropSeason::factory()->create([
        'team_id' => null,
        'title' => 'Auto-filled Season',
        'is_current' => false,
    ]);

    $resource = FarmingResource::factory()->create([
        'team_id' => null,
        'title' => 'Auto-filled Resource',
    ]);

    expect($season->team_id)->toBe($admin->team_id)
        ->and($resource->team_id)->toBe($admin->team_id);
});

it('throws when creating without team_id and the authenticated user has no team', function () {
    $userWithoutTeam = User::factory()->admin()->make(['team_id' => null]);
    test()->actingAs($userWithoutTeam);

    expect(fn () => CropSeason::factory()->create([
        'team_id' => null,
        'title' => 'Orphan Season',
        'is_current' => false,
    ]))->toThrow(ModelNotFoundException::class, 'No team_id set in user.');
});
