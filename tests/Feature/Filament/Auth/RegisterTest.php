<?php

use App\Enums\Role;
use App\Filament\Pages\Auth\Register;
use App\Models\CropSeason;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;

use function Pest\Livewire\livewire;

beforeEach(function () {
    Filament::setCurrentPanel('admin');
});

it('registers an admin user with a team and current crop season', function () {
    $name = 'Mansoor Landlord';
    $email = 'mansoor@example.com';

    livewire(Register::class)
        ->fillForm([
            'name' => $name,
            'email' => $email,
            'password' => 'password',
            'passwordConfirmation' => 'password',
        ])
        ->call('register')
        ->assertHasNoFormErrors()
        ->assertRedirect(Filament::getUrl());

    $this->assertAuthenticated();

    $user = User::query()->where('email', $email)->first();

    expect($user)->not->toBeNull()
        ->and($user->name)->toBe($name)
        ->and($user->role)->toBe(Role::Admin)
        ->and($user->team_id)->not->toBeNull();

    $team = Team::query()->find($user->team_id);

    expect($team)->not->toBeNull()
        ->and($team->name)->toBe($name . "'s Team");

    $season = CropSeason::query()
        ->where('team_id', $team->id)
        ->where('is_current', true)
        ->first();

    expect($season)->not->toBeNull()
        ->and($season->title)->toBe('Season-' . now()->year);
});
