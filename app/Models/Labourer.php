<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Labourer extends Model
{
    use BelongsToTeam, HasFactory;

    public function cottonPickingDailies(): HasMany
    {
        return $this->hasMany(CottonPickingDaily::class);
    }
}