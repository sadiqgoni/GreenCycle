<?php

namespace App\Filament\Admin\Resources\AccountInformationResource\Pages;

use App\Filament\Admin\Resources\AccountInformationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAccountInformation extends EditRecord
{
    protected static string $resource = AccountInformationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
