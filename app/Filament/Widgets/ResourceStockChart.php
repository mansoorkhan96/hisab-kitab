<?php

namespace App\Filament\Widgets;

use App\Enums\FarmingResourceType;
use App\Models\CropSeason;
use App\Models\FarmingResource;
use App\Models\Ledger;
use Filament\Widgets\ChartWidget;

class ResourceStockChart extends ChartWidget
{
    protected ?string $heading = 'Resource Stock';

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '300px';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $currentSeason = CropSeason::where('is_current', true)->first();

        if (! $currentSeason) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $farmingResources = FarmingResource::query()
            ->where('type', '!=', FarmingResourceType::Implement)
            ->withWhereHas('resourceStocks', fn ($query) => $query->whereBelongsTo($currentSeason))
            ->get();

        $labels = [];
        $availableStockData = [];
        $consumedStockData = [];

        foreach ($farmingResources as $resource) {
            $totalStock = $resource->resourceStocks->sum('quantity');

            $consumed = Ledger::where('farming_resource_id', $resource->id)
                ->whereBelongsTo($currentSeason)
                ->sum('quantity');

            $available = $totalStock - $consumed;

            $labels[] = $resource->title;
            $availableStockData[] = (float) max(0, $available); // Ensure non-negative
            $consumedStockData[] = (float) $consumed;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Available Stock',
                    'data' => $availableStockData,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.8)', // green
                    'borderColor' => 'rgba(34, 197, 94, 1)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Consumed',
                    'data' => $consumedStockData,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.8)', // red
                    'borderColor' => 'rgba(239, 68, 68, 1)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            // 'responsive' => true,
            // 'maintainAspectRatio' => false,
            'scales' => [
                'x' => [
                    'stacked' => true,
                ],
                'y' => [
                    'stacked' => true,
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Quantity',
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ],
            ],
            'interaction' => [
                'mode' => 'nearest',
                'axis' => 'x',
                'intersect' => false,
            ],
        ];
    }
}
