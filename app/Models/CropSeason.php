<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CropSeason extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'rates' => AsCollection::class,
    ];

    protected static function booted()
    {
        // static::creating(function (CropSeason $cropSeason) {
        //     if (blank($cropSeason->rates)) {
        //         $farmingResources = FarmingResource::all()->map(fn (FarmingResource $farmingResource) => [
        //             'farming_resource_id' => $farmingResource->id,
        //             'rate' => 0,
        //         ]);

        //         $cropSeason->rates = $farmingResources;
        //     }
        // });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
