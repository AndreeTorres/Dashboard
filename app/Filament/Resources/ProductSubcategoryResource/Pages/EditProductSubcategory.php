<?php

namespace App\Filament\Resources\ProductSubcategoryResource\Pages;

use App\Filament\Resources\ProductSubcategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductSubcategory extends EditRecord
{
    protected static string $resource = ProductSubcategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
