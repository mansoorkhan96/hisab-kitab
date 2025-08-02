<?php

namespace App\Filament\Widgets;

use App\Models\CropSeason;
use App\Models\Tractor;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;

class TractorStatsWidget extends BaseWidget
{
    public ?Model $record = null;

    protected function getStats(): array
    {
        if (! $this->record instanceof Tractor) {
            return [];
        }

        $currentSeason = CropSeason::where('is_current', true)->first();

        if (! $currentSeason) {
            return [
                Stat::make('No Current Season', 'Please set a current crop season')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('warning'),
            ];
        }

        // Calculate Total Expenses
        $totalExpenses = $this
            ->record
            ->expenses()
            ->whereBelongsTo($currentSeason)
            ->sum('amount');

        // Calculate Total Revenue from Ledgers
        $ledgerRevenue = $this
            ->record
            ->ledgers()
            ->whereBelongsTo($currentSeason)
            ->sum('amount');

        // Calculate Revenue from Threshings (convert sacks to monetary value)
        // Assuming we need to convert threshing_charges_in_sacks to money using wheat rate
        $threshingRevenue = $this
            ->record
            ->threshings()
            ->whereHas('calculation', fn ($query) => $query->whereBelongsTo($currentSeason))
            ->sum('threshing_charges_in_sacks') * $currentSeason->wheat_rate;

        $totalRevenue = $ledgerRevenue + $threshingRevenue;
        $netIncome = $totalRevenue - $totalExpenses;

        return [
            Stat::make('Total Expenses', 'PKR '.number_format($totalExpenses, 2))
                ->color('danger'),
            Stat::make('Total Revenue', 'PKR '.number_format($totalRevenue, 2))
                ->color('success'),
            Stat::make('Net Income', 'PKR '.number_format($netIncome, 2))
                ->color($netIncome >= 0 ? 'success' : 'danger'),
        ];
    }
}
