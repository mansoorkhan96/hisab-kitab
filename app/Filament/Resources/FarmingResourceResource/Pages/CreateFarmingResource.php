<?php

namespace App\Filament\Resources\FarmingResourceResource\Pages;

use App\Filament\Resources\FarmingResourceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFarmingResource extends CreateRecord
{
    protected static string $resource = FarmingResourceResource::class;

    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     $data['user_id'] = auth()->id();

    //     return $data;
    // }
}
