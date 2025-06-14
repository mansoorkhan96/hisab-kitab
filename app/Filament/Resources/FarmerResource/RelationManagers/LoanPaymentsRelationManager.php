<?php

namespace App\Filament\Resources\FarmerResource\RelationManagers;

use App\Models\Calculation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LoanPaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'loanPayments';

    protected static ?string $icon = 'heroicon-o-banknotes';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('calculation_id')
                    ->relationship('calculation', 'id', fn (Builder $query) => $query->where('farmer_id', $this->getOwnerRecord()->id)->with('cropSeason'))
                    ->getOptionLabelFromRecordUsing(fn (Calculation $calculation) => $calculation->cropSeason->name)
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('amount')
            ->columns([
                Tables\Columns\TextColumn::make('calculation.cropSeason.name')
                    ->label('Calculation'),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('PKR')
                    ->summarize(Sum::make()->label('Total Amount')->money('PKR')),
                Tables\Columns\TextColumn::make('notes')
                    ->label('Notes'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
