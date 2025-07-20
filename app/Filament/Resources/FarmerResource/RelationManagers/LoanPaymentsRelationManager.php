<?php

namespace App\Filament\Resources\FarmerResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Models\Calculation;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LoanPaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'loanPayments';

    protected static string | \BackedEnum | null $icon = 'heroicon-o-banknotes';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('calculation_id')
                    ->relationship('calculation', 'id', fn (Builder $query) => $query->where('farmer_id', $this->getOwnerRecord()->id)->with('cropSeason'))
                    ->getOptionLabelFromRecordUsing(fn (Calculation $calculation) => $calculation->cropSeason->name)
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->maxLength(255),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('amount')
            ->columns([
                TextColumn::make('calculation.cropSeason.name')
                    ->label('Calculation'),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('PKR')
                    ->summarize(Sum::make()->label('Total Amount')->money('PKR')),
                TextColumn::make('notes')
                    ->label('Notes'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
