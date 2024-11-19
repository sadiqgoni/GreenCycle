<?php

namespace App\Filament\Company\Resources\CompanyServiceRequestResource\Pages;

use App\Filament\Company\Resources\CompanyServiceRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditServiceRequest extends EditRecord
{
    protected static string $resource = CompanyServiceRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
