<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Calculation extends Model
{
    use HasFactory;

    protected $casts = [
        'finalized_at' => 'datetime',
    ];

    public function cropSeason(): BelongsTo
    {
        return $this->belongsTo(CropSeason::class);
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }
}
