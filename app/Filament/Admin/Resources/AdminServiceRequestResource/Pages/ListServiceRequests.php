<?php

namespace App\Filament\Admin\Resources\AdminServiceRequestResource\Pages;

use App\Filament\Admin\Resources\AdminServiceRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListServiceRequests extends ListRecords
{
    protected static string $resource = AdminServiceRequestResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\CreateAction::make(),
    //     ];
    // }
}
