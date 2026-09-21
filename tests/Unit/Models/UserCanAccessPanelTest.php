<?php

use App\Models\User;
use Filament\Panel;
use Tests\TestCase;

uses(TestCase::class);

it('allows admin users to access the panel', function () {
    $user = User::factory()->admin()->make(['team_id' => 1]);

    expect($user->canAccessPanel(Panel::make()))->toBeTrue();
});

it('denies farmer users access to the panel', function () {
    $user = User::factory()->farmer()->make(['team_id' => 1]);

    expect($user->canAccessPanel(Panel::make()))->toBeFalse();
});

it('denies driver users access to the panel', function () {
    $user = User::factory()->driver()->make(['team_id' => 1]);

    expect($user->canAccessPanel(Panel::make()))->toBeFalse();
});
