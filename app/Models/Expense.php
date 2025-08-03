<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Expense extends Model
{
    use BelongsToTeam;

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function expensable(): MorphTo
    {
        return $this->morphTo();
    }

    public function cropSeason(): BelongsTo
    {
        return $this->belongsTo(CropSeason::class);
    }
}
