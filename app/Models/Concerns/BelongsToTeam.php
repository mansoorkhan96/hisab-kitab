<?php

namespace App\Models\Concerns;

use App\Models\Team;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

trait BelongsToTeam
{
    public static function bootBelongsToTeam(): void
    {
        static::creating(function ($model) {
            if (empty($model->team_id)) {
                $teamId = auth()->user()->team_id;

                if ($teamId) {
                    $model->team_id = $teamId;
                } else {
                    Log::error('No team_id found for user '.auth()->id());

                    throw new ModelNotFoundException('No team_id set in user.');
                }
            }
        });

        static::addGlobalScope('team', function (Builder $query) {
            if (auth()->hasUser()) {
                $query->whereBelongsTo(auth()->user()->team);
            }
        });
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
