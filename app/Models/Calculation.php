<?php

namespace App\Models;

use App\Enums\CropType;
use App\Models\Concerns\BelongsToTeam;
use App\ValueObjects\CottonCropCalculationReport;
use App\ValueObjects\WheatCropCalculationReport;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Calculation extends Model
{
    use BelongsToTeam, HasFactory;

    protected $casts = [
        'crop_type' => CropType::class,
    ];

    protected static function booted()
    {
        static::saving(function (Calculation $calculation) {
            $result = match ($calculation->crop_type) {
                CropType::Wheat => WheatCropCalculationReport::make($calculation),
                CropType::Cotton => CottonCropCalculationReport::make($calculation),
            };

            $calculation->landlord_revenue = $result->landlordRevenue;
            $calculation->landlord_net_income = $result->landlordRevenue + $result->machineAmount;
            $calculation->farmer_gross_revenue = $result->farmerGrossRevenue;
            $calculation->farmer_revenue = $result->farmerRevenue;
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

    public function threshings(): HasMany
    {
        return $this->hasMany(Threshing::class);
    }

    public function loanPayments(): HasMany
    {
        return $this->hasMany(LoanPayment::class);
    }

    public function cottonPickingRounds(): HasMany
    {
        return $this->hasMany(CottonPickingRound::class, 'crop_season_id', 'crop_season_id')
            ->where('user_id', $this->user_id);
    }
}
