<?php

namespace App\Filament\Resources\FarmerResource\Widgets;

use App\Models\FarmerLoan;
use App\Models\LoanPayment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;

class LoanWidget extends BaseWidget
{
    public ?Model $record = null;

    public int $columns = 1;

    protected function getStats(): array
    {
        $loanAmount = FarmerLoan::query()
            ->whereBelongsTo($this->record)
            ->sum('amount');

        $loanPayments = LoanPayment::query()
            ->whereBelongsTo($this->record)
            ->sum('amount');

        $value = $loanAmount - $loanPayments;

        return [
            Stat::make('Total Loan', Number::currency($value, 'PKR')),
        ];
    }

    protected function getColumns(): int
    {
        return $this->columns;
    }
}
