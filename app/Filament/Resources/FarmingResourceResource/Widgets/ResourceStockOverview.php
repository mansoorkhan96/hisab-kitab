<?php

namespace App\Filament\Resources\FarmingResourceResource\Widgets;

use App\Enums\FarmingResourceType;
use App\Models\CropSeason;
use App\Models\FarmingResource;
use App\Models\Ledger;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;

class ResourceStockOverview extends BaseWidget
{
    public ?Model $record = null;

    protected function getStats(): array
    {
        if (! $this->record instanceof FarmingResource || $this->record->type === FarmingResourceType::Implement) {
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

        $totalStock = $this->record->resourceStocks()
            ->where('crop_season_id', $currentSeason->id)
            ->sum('quantity');

        $totalConsumed = Ledger::where('farming_resource_id', $this->record->id)
            ->where('crop_season_id', $currentSeason->id)
            ->sum('quantity');

        $remainingStock = $totalStock - $totalConsumed;

        return [
            Stat::make('Total Stock', number_format($totalStock, 2))
                ->description('For current season')
                ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before)
                ->color('success'),

            Stat::make('Consumed', number_format($totalConsumed, 2))
                ->description('Used this season')
                ->descriptionIcon('heroicon-m-arrow-trending-down', IconPosition::Before)
                ->color('warning'),

            Stat::make('Remaining', number_format($remainingStock, 2))
                ->description('Available stock')
                ->descriptionIcon('heroicon-m-cube', IconPosition::Before)
                ->color($remainingStock > 0 ? 'primary' : 'danger'),
        ];
    }
}
