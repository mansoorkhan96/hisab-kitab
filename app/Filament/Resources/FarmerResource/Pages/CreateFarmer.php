<?php

namespace App\Filament\Resources\FarmerResource\Pages;

use App\Filament\Resources\FarmerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFarmer extends CreateRecord
{
    protected static string $resource = FarmerResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }
}
