<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Threshing extends Model
{
    use HasFactory;

    public function calculation(): BelongsTo
    {
        return $this->belongsTo(Calculation::class);
    }

    public function tractor(): BelongsTo
    {
        return $this->belongsTo(Tractor::class);
    }
}
