<?php

namespace App\Filament\Pages;

use Filament\Facades\Filament;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return collect(Filament::getWidgets())->filter(function ($widget) {
            return is_string($widget) && $widget === 'Filament\Widgets\AccountWidget';
        })->all();
    }
}
