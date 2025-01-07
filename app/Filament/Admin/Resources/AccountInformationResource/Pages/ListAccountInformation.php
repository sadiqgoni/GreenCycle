<?php

namespace App\Filament\Admin\Resources\AccountInformationResource\Pages;

use App\Filament\Admin\Resources\AccountInformationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAccountInformation extends ListRecords
{
    protected static string $resource = AccountInformationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
