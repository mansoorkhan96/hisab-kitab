<?php

use App\Models\CropSeason;

it('clears the previous current season on the same team when another is set current', function () {
    $admin = actingAsAdmin();
    $team = $admin->team;

    $seasonA = makeCurrentSeason($team, ['title' => 'Season A']);
    $seasonB = CropSeason::factory()->create([
        'team_id' => $team->id,
        'title' => 'Season B',
        'is_current' => false,
    ]);

    $seasonB->update(['is_current' => true]);

    expect($seasonA->fresh()->is_current)->toBeFalse()
        ->and($seasonB->fresh()->is_current)->toBeTrue();
});
