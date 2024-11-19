<?php

namespace App\Filament\Household\Resources\HouseholdServiceRequestResource\Pages;

use App\Filament\Household\Resources\HouseholdServiceRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditServiceRequest extends EditRecord
{
    protected static string $resource = HouseholdServiceRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
