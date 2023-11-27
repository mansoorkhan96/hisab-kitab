<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ledger extends Model
{
    use HasFactory;

    public function cropSeason(): BelongsTo
    {
        return $this->belongsTo(CropSeason::class);
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }

    public function farmingResource(): BelongsTo
    {
        return $this->belongsTo(FarmingResource::class);
    }
}
