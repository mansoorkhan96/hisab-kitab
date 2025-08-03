<?php

namespace App\Filament\Resources;

use App\Enums\Role;
use App\Filament\Resources\Calculations\RelationManagers\ThreshingsRelationManager;
use App\Filament\Resources\TractorResource\Pages\CreateTractor;
use App\Filament\Resources\TractorResource\Pages\EditTractor;
use App\Filament\Resources\TractorResource\Pages\ListTractors;
use App\Filament\Resources\TractorResource\RelationManagers\ExpensesRelationManager;
use App\Filament\Resources\Users\RelationManagers\LedgersRelationManager;
use App\Models\Tractor;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TractorResource extends Resource
{
    protected static ?string $model = Tractor::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-truck';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Select::make('user_id')
                    ->label('Driver')
                    ->relationship('user', 'name', fn (Builder $query) => $query->where('role', Role::Driver))
                    ->required()
                    ->searchable()
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Driver')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            LedgersRelationManager::class,
            ThreshingsRelationManager::class,
            ExpensesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTractors::route('/'),
            'create' => CreateTractor::route('/create'),
            'edit' => EditTractor::route('/{record}/edit'),
        ];
    }
}
