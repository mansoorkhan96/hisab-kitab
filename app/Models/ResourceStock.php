<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResourceStock extends Model
{
    use BelongsToTeam;
    use HasFactory;

    protected $casts = [
        'date' => 'date',
        'quantity' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function cropSeason(): BelongsTo
    {
        return $this->belongsTo(CropSeason::class);
    }

    public function farmingResource(): BelongsTo
    {
        return $this->belongsTo(FarmingResource::class);
    }
}
