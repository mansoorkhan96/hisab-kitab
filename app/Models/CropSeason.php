<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CropSeason extends Model
{
    use BelongsToTeam, HasFactory, SoftDeletes;

    protected $casts = [
        'is_current' => 'boolean',
    ];

    protected static function booted()
    {
        static::saving(function (CropSeason $cropSeason) {
            if ($cropSeason->is_current) {
                self::query()
                    ->where('id', '!=', $cropSeason->id)
                    ->update(['is_current' => false]);
            }
        });
    }

    public static function current(): self
    {
        return self::query()
            ->where('is_current', true)
            ->first();
    }
}
