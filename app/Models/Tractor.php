<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tractor extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'user_id',
    ];

    public static function booted(): void
    {
        static::creating(function (Tractor $tractor) {
            if (empty($tractor->user_id)) {
                $tractor->user_id = auth()->id();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function threshings(): HasMany
    {
        return $this->hasMany(Threshing::class);
    }
}
