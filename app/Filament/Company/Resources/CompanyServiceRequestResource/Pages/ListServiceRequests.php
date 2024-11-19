<?php

namespace App\Filament\Company\Resources\CompanyServiceRequestResource\Pages;

use App\Filament\Company\Resources\CompanyServiceRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListServiceRequests extends ListRecords
{
    protected static string $resource = CompanyServiceRequestResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\CreateAction::make(),
    //     ];
    // }
}
