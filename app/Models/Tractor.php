<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Tractor extends Model
{
    use BelongsToTeam, HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function threshings(): HasMany
    {
        return $this->hasMany(Threshing::class);
    }

    public function expenses(): MorphMany
    {
        return $this->morphMany(Expense::class, 'expensable');
    }

    public function ledgers(): HasMany
    {
        return $this->hasMany(Ledger::class);
    }
}
