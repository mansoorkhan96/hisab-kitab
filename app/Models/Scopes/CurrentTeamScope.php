<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Log;

class CurrentTeamScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (app()->runningInConsole()) {
            return;
        }

        $teamId = auth()->user()->team_id;

        if ($teamId) {
            $builder->where($model->getTable().'.team_id', $teamId);
        } else {
            Log::error('CurrentTeamScope: No team_id found for user '.auth()->id());

            throw new ModelNotFoundException('CurrentTeamScope: No team_id set in the session or on the user.');
        }
    }
}
