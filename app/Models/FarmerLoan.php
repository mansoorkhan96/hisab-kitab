<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmerLoan extends Model
{
    use HasFactory;

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }
}
