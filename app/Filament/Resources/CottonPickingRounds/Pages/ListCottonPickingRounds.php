<?php

namespace App\Filament\Resources\CottonPickingRounds\Pages;

use App\Filament\Resources\CottonPickingRounds\CottonPickingRoundResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCottonPickingRounds extends ListRecords
{
    protected static string $resource = CottonPickingRoundResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
