<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanPayment extends Model
{
    use HasFactory;

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }

    public function calculation(): BelongsTo
    {
        return $this->belongsTo(Calculation::class);
    }
}
