<?php

namespace App\Filament\Admin\Resources\AdminServiceRequestResource\Pages;

use App\Filament\Admin\Resources\AdminServiceRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditServiceRequest extends EditRecord
{
    protected static string $resource = AdminServiceRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
