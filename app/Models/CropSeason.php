<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User;

class CropSeason extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'is_current' => 'boolean',
    ];

    protected static function booted()
    {
        static::saving(function (CropSeason $cropSeason) {
            if ($cropSeason->is_current) {
                self::query()
                    ->where('user_id', auth()->id())
                    ->where('id', '!=', $cropSeason->id)
                    ->update(['is_current' => false]);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function current(): self
    {
        return self::query()
            ->where('is_current', true)
            ->where('user_id', auth()->id())
            ->first();
    }
}
