<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FarmerLoanResource\Pages;
use App\Filament\Resources\FarmerResource\RelationManagers\FarmerLoansRelationManager;
use App\Models\FarmerLoan;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\ActionSize;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FarmerLoanResource extends Resource
{
    protected static ?string $model = FarmerLoan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('amount')
                    ->numeric()
                    ->required(),
                Textarea::make('purpose')
                    ->maxLength(1000)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('purpose')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('amount')
                    ->money('PKR')
                    ->summarize(Sum::make()->label('Total Amount')->money('PKR')),
                TextColumn::make('paid_at')
                    ->description(fn (FarmerLoan $farmerLoan) => $farmerLoan->paid_at?->format('F j, Y'))
                    ->getStateUsing(
                        fn (FarmerLoan $farmerLoan) => filled($farmerLoan->paid_at)
                            ? 'Paid'
                            : 'Unpaid'
                    )
                    ->icon(
                        fn (FarmerLoan $farmerLoan) => filled($farmerLoan->paid_at)
                        ? 'heroicon-m-check-badge'
                        : 'heroicon-m-x-circle'
                    )
                    ->color(fn (FarmerLoan $farmerLoan) => filled($farmerLoan->paid_at)
                        ? 'success'
                        : 'danger'
                    ),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('mark_as_paid')
                    ->label('Mark As Paid')
                    ->visible(fn (FarmerLoan $farmerLoan) => empty($farmerLoan->paid_at))
                    ->button()
                    ->size(ActionSize::ExtraSmall)
                    ->requiresConfirmation()
                    ->icon('heroicon-m-check-badge')
                    ->action(fn (FarmerLoan $farmerLoan) => $farmerLoan->update(['paid_at' => now()])),
                Tables\Actions\EditAction::make()
                    ->visible(fn ($livewire) => $livewire instanceof FarmerLoansRelationManager),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFarmerLoans::route('/'),
            'create' => Pages\CreateFarmerLoan::route('/create'),
            'edit' => Pages\EditFarmerLoan::route('/{record}/edit'),
        ];
    }
}
