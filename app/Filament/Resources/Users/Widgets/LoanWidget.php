<?php

namespace App\Filament\Resources\Users\Widgets;

use App\Models\Loan;
use App\Models\LoanPayment;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class LoanWidget extends BaseWidget
{
    public ?User $record = null;

    public array|int|null $columns = 1;

    protected $listeners = [
        '$refresh' => '$refresh',
    ];

    protected function getStats(): array
    {
        $loanAmount = Loan::query()
            ->whereBelongsTo($this->record)
            ->sum('amount');

        $loanPayments = LoanPayment::query()
            ->whereBelongsTo($this->record)
            ->sum('amount');

        $value = $loanAmount - $loanPayments;

        return [
            Stat::make('Total Loan', Number::currency($value, 'PKR'))
                ->extraAttributes([
                    'class' => 'user-total-loan '.($value > 0 ? 'text-success' : 'text-danger'),
                ]),
        ];
    }

    protected function getColumns(): int
    {
        return $this->columns;
    }
}
