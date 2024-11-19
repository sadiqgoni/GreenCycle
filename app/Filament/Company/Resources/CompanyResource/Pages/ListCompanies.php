<?php

namespace App\Filament\Company\Resources\CompanyResource\Pages;

use App\Filament\Company\Resources\CompanyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCompanies extends ListRecords
{
    protected static string $resource = CompanyResource::class;

    protected function getHeaderActions(): array
    {
        $user = auth()->user();
        return [
            Actions\CreateAction::make()
            ->hidden(fn() => $user->role === 'admin')

        ];
    }
}
