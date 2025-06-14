<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FarmerLoanResource\Pages;
use App\Filament\Resources\FarmerResource\RelationManagers\FarmerLoansRelationManager;
use App\Models\FarmerLoan;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
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
            ->heading('Farmer Loans (Think of it just like a history)')
            ->columns([
                TextColumn::make('purpose')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('amount')
                    ->money('PKR')
                    ->summarize(Sum::make()->label('Total Amount')->money('PKR')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn ($livewire) => $livewire instanceof FarmerLoansRelationManager),
                Tables\Actions\DeleteAction::make()
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
