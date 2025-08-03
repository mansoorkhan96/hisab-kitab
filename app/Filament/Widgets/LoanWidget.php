<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class LoanWidget extends BaseWidget
{
    public ?User $record = null;

    public array|int|null $columns = 1;

    protected static bool $isDiscovered = false;

    protected $listeners = [
        '$refresh' => '$refresh',
    ];

    protected function getStats(): array
    {
        $value = $this->record->outstandingLoanBalance;

        return [
            Stat::make('Total Loan', Number::currency($value, 'PKR'))
                ->extraAttributes([
                    'class' => $value > 0 ?
                        '[&_.fi-wi-stats-overview-stat-value]:text-(--danger-500)' :
                        '[&_.fi-wi-stats-overview-stat-value]:text-(--success-500)',
                ]),
        ];
    }

    protected function getColumns(): int
    {
        return $this->columns;
    }
}
