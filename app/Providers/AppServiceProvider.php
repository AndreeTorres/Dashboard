<?php

namespace App\Providers;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\Column;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! app()->isLocal()) {
            URL::forceScheme('https');
        }

        Table::$defaultNumberLocale = 'es_HN';
        Table::$defaultDateDisplayFormat = 'd/m/Y';
        Table::$defaultDateTimeDisplayFormat = 'd/m/Y H:i:s';
        Table::$defaultTimeDisplayFormat = 'H:i';

        $this->autoTranslateLabels();
    }

    private function autoTranslateLabels()
    {
        $this->translateLabels([
            Field::class,
            BaseFilter::class,
            Placeholder::class,
            Column::class,

            // Filters
            Filter::class,

            // Infolists:
            TextEntry::class,
        ]);
    }

    private function translateLabels(array $components = [])
    {
        foreach ($components as $component) {
            $component::configureUsing(function ($c): void {
                $c->translateLabel();
            });
        }
    }
}
