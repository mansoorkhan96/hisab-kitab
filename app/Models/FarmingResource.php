<?php

namespace App\Models;

use App\Enums\FarmingResourceType;
use App\Models\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FarmingResource extends Model
{
    use BelongsToTeam, HasFactory;

    protected $casts = [
        'type' => FarmingResourceType::class,
    ];

    public function resourceStocks(): HasMany
    {
        return $this->hasMany(ResourceStock::class);
    }
}
