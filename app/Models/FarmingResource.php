<?php

namespace App\Models;

use App\Enums\FarmingResourceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FarmingResource extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'type' => FarmingResourceType::class,
    ];

    // public function user(): BelongsTo
    // {
    //     return $this->belongsTo(User::class);
    // }
}
