<?php

use App\Models\FarmingResource;
use App\Models\Ledger;
use App\Models\User;

it('copies the farming resource rate when ledger rate is null', function () {
    $admin = actingAsAdmin();
    $team = $admin->team;
    $season = makeCurrentSeason($team);
    $farmer = User::factory()->farmer()->create(['team_id' => $team->id]);

    $resource = FarmingResource::factory()->create([
        'team_id' => $team->id,
        'rate' => 1_234.50,
    ]);

    $ledger = Ledger::factory()->create([
        'user_id' => $farmer->id,
        'crop_season_id' => $season->id,
        'farming_resource_id' => $resource->id,
        'quantity' => 2,
        'rate' => null,
    ]);

    expect((float) $ledger->fresh()->rate)->toBe(1_234.50);
});
