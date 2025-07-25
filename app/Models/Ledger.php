<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ledger extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::saving(function (Ledger $ledger) {
            if (empty($ledger->rate)) {
                $ledger->rate = $ledger->farmingResource->rate;
            }
        });
    }

    public function cropSeason(): BelongsTo
    {
        return $this->belongsTo(CropSeason::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function farmingResource(): BelongsTo
    {
        return $this->belongsTo(FarmingResource::class);
    }

    public function quantityWithUnit(): Attribute
    {
        return Attribute::get(
            fn () => str($this->farmingResource->quantity_unit)
                ->plural($this->quantity)
                ->prepend(' ')
        );
    }
}
