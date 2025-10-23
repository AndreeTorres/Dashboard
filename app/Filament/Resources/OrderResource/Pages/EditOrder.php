<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected $listeners = ['refreshOwnerRecord' => 'refreshFormData'];

    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }

    protected function authorizeAccess(): void
    {
        // Abort if the record is not editable, return HTTP 403 Forbidden
        abort_unless($this->record->isEditable(), 403);
    }
}
