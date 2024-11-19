<?php

namespace App\Filament\Company\Resources\CompanyResource\Pages;

use App\Filament\Company\Resources\CompanyResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCompany extends CreateRecord
{
    protected static string $resource = CompanyResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }

    protected function getRedirectUrl(): string{
        return $this->getResource()::getUrl('index');
    }
}
