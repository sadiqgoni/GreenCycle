<?php

namespace App\Filament\Household\Resources\HouseholdServiceRequestResource\Pages;

use App\Filament\Household\Resources\HouseholdServiceRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateServiceRequest extends CreateRecord
{
    protected static string $resource = HouseholdServiceRequestResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['household_id'] = auth()->id();
        return $data;
    }

    protected function getRedirectUrl(): string{
        return $this->getResource()::getUrl('index');
    }

}
