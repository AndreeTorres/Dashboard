<?php

namespace App\Filament\Resources\SupplyCategoryResource\Pages;

use App\Filament\Resources\SupplyCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSupplyCategory extends ViewRecord
{
    protected static string $resource = SupplyCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
