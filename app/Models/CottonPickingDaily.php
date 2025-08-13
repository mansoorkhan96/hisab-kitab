<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CottonPickingDaily extends Model
{
    use HasFactory;

    protected $casts = [
        'picking_date' => 'date',
    ];

    public function cottonPickingRound(): BelongsTo
    {
        return $this->belongsTo(CottonPickingRound::class);
    }

    public function labourer(): BelongsTo
    {
        return $this->belongsTo(Labourer::class);
    }
}
