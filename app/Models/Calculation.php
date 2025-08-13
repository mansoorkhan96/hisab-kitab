<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use App\ValueObjects\WheatCropCalculationReport;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Calculation extends Model
{
    use BelongsToTeam, HasFactory;

    protected static function booted()
    {
        static::saving(function (Calculation $calculation) {
            $result = WheatCropCalculationReport::make($calculation);

            $calculation->landlord_amount = $result->landlordAmount;
            $calculation->landlord_net_income = $result->landlordAmount + $result->machineAmount;
            $calculation->farmer_amount = $result->farmerAmount;
            $calculation->farmer_profit_loss = $result->farmerProfitLoss;
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
