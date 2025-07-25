<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Expense extends Model
{
    protected $fillable = [
        'crop_season_id',
        'title',
        'amount',
        'details',
        'date',
        'expensable_type',
        'expensable_id',
    ];

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
