<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function authorizeAccess(): void
    {
        if ($this->record->id === Auth::id()) {
            abort(403);
        }

        abort_unless(static::getResource()::canEdit($this->getRecord()), 403);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
