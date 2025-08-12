<?php

namespace App\Filament\Resources\CottonPickingRounds\Pages;

use App\Filament\Resources\CottonPickingRounds\CottonPickingRoundResource;
use Filament\Resources\Pages\EditRecord;

class EditCottonPickingRound extends EditRecord
{
    protected static string $resource = CottonPickingRoundResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
