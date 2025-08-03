<?php

namespace App\Models\Concerns;

use App\Models\Scopes\CurrentTeamScope;
use App\Models\Team;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

trait BelongsToTeam
{
    public static function bootBelongsToTeam(): void
    {
        static::creating(function ($model) {
            if (empty($model->team_id)) {
                $teamId = auth()->user()?->team_id ?? 1; // Default to team 1 as requested

                if ($teamId) {
                    $model->team_id = $teamId;
                } else {
                    Log::error('CurrentTeamScope: No team_id found for user '.auth()->id());

                    throw new ModelNotFoundException('CurrentTeamScope: No team_id set in the session, user, or database.');
                }
            }
        });

        static::addGlobalScope(new CurrentTeamScope);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}