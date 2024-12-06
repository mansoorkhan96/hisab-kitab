<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\FarmerLoanResource;
use App\Models\FarmerLoan;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Livewire\Attributes\Reactive;

class FarmerLoansTableWidget extends BaseWidget
{
    protected static bool $isDiscovered = false;

    #[Reactive]
    public $farmer_id;

    public function table(Table $table): Table
    {
        return FarmerLoanResource::table($table)
            ->heading('Farmer Loans')
            ->query(FarmerLoan::where('farmer_id', $this->farmer_id))
            ->headerActions([
                Action::make('Add new')
                    ->form(fn (Form $form) => FarmerLoanResource::form($form)->columns(2))
                    ->action(function (array $data) {
                        $data['farmer_id'] = $this->farmer_id;

                        FarmerLoan::create($data);

                        Notification::make()->success()->body('Farmer Loan was created!');
                    }),
            ])
            ->paginated(false);
    }
}
