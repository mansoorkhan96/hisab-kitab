<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Expense extends Model
{
    protected $fillable = [
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
}
