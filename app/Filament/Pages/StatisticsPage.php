<?php

namespace App\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;

class StatisticsPage extends Page
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.pages.statistics-page';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('Statistics');
    }

    public function getTitle(): string
    {
        return __('Statistics Dashboard');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\StatisticsExpensesWidget::class,
            \App\Filament\Widgets\StatisticsSalesWidget::class,
            \App\Filament\Widgets\StatisticsCrossMetricsWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            \App\Filament\Widgets\SalesVsExpensesChart::class,
            \App\Filament\Widgets\MonthlyComparisonChart::class,
            \App\Filament\Widgets\ExpenseDistributionChart::class,
            \App\Filament\Widgets\SalesDistributionChart::class,
            \App\Filament\Widgets\TopSubcategoriesChart::class,
            \App\Filament\Widgets\TopProductsChart::class,
            \App\Filament\Widgets\PerformanceRadarChart::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 3;
    }

    public function getFooterWidgetsColumns(): int|array
    {
        return [
            'default' => 1,
            'sm' => 2,
            'lg' => 2,
        ];
    }
}
