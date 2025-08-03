<?php

namespace App\Filament\Pages\Auth;

use App\Enums\Role;
use App\Models\Team;

class Register extends \Filament\Auth\Pages\Register
{
    protected function mutateFormDataBeforeRegister(array $data): array
    {
        $name = $data['name'];

        $team = Team::create([
            'name' => $name."'s Team",
        ]);

        $team->cropSeasons()->createQuietly([
            'title' => 'Season-'.now()->year,
            'is_current' => true,
        ]);

        $data['team_id'] = $team->id;
        $data['role'] = Role::Admin;

        return $data;
    }
}
