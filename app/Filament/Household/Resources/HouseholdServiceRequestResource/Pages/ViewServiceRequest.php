<?php
namespace App\Filament\Household\Resources\HouseholdServiceRequestResource\Pages;

use App\Filament\Household\Resources\HouseholdServiceRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewServiceRequest extends ViewRecord
{
    protected static string $resource = HouseholdServiceRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
