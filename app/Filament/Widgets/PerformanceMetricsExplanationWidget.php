<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class PerformanceMetricsExplanationWidget extends Widget
{
    protected static string $view = 'filament.widgets.performance-metrics-explanation';

    protected static ?int $sort = 11;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return true;
    }

    public function getViewData(): array
    {
        return [
            'metrics' => [
                [
                    'name' => __('Sales Volume'),
                    'description' => __('Measures total sales revenue. Higher values indicate stronger sales performance.'),
                    'icon' => 'heroicon-o-chart-bar-square',
                    'color' => 'success',
                ],
                [
                    'name' => __('Cost Control'),
                    'description' => __('Evaluates expense management efficiency. Higher values mean better cost control.'),
                    'icon' => 'heroicon-o-shield-check',
                    'color' => 'warning',
                ],
                [
                    'name' => __('Order Frequency'),
                    'description' => __('Tracks the number of orders received. More orders indicate higher customer activity.'),
                    'icon' => 'heroicon-o-shopping-cart',
                    'color' => 'info',
                ],
                [
                    'name' => __('Order Value'),
                    'description' => __('Average amount per order. Higher values suggest premium sales or upselling success.'),
                    'icon' => 'heroicon-o-currency-dollar',
                    'color' => 'success',
                ],
                [
                    'name' => __('Profit Margin'),
                    'description' => __('Percentage of revenue remaining after expenses. Key profitability indicator.'),
                    'icon' => 'heroicon-o-calculator',
                    'color' => 'primary',
                ],
                [
                    'name' => __('Efficiency'),
                    'description' => __('Sales-to-expense ratio. Higher values indicate more revenue generated per dollar spent.'),
                    'icon' => 'heroicon-o-bolt',
                    'color' => 'danger',
                ],
            ],
        ];
    }
}
