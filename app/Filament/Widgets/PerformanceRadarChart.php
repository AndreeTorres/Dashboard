<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class PerformanceRadarChart extends ChartWidget
{
    protected static ?string $heading = null;

    protected int|string|array $columnSpan = 2;

    public function getHeading(): string
    {
        return __('Business Performance Metrics');
    }

    protected static ?int $sort = 7;

    protected static ?string $maxHeight = '800px';

    protected function getData(): array
    {
        $last30Days = Carbon::now()->subDays(30);

        // Obtener datos
        $sales30Days = Order::where('created_at', '>=', $last30Days)->sum('total');
        $expenses30Days = Expense::where('date', '>=', $last30Days)->sum('cost');
        $orders30Days = Order::where('created_at', '>=', $last30Days)->count();
        $avgOrderValue = $orders30Days > 0 ? $sales30Days / $orders30Days : 0;

        // Calcular métricas normalizadas (0-100)
        $maxSales = 10000; // Valor máximo esperado para normalizar
        $maxExpenses = 5000;
        $maxOrders = 200;
        $maxAvgOrder = 100;
        $maxProfit = 5000;
        $maxEfficiency = 100;

        $profitMargin = $sales30Days > 0 ? (($sales30Days - $expenses30Days) / $sales30Days) * 100 : 0;
        $efficiency = $expenses30Days > 0 ? (($sales30Days / $expenses30Days) - 1) * 100 : 0;
        $efficiency = max(0, min(100, $efficiency)); // Limitar entre 0-100

        return [
            'datasets' => [
                [
                    'label' => __('Current Performance'),
                    'data' => [
                        min(100, ($sales30Days / $maxSales) * 100),        // Sales Volume
                        min(100, (($maxExpenses - $expenses30Days) / $maxExpenses) * 100), // Cost Control (inverso)
                        min(100, ($orders30Days / $maxOrders) * 100),      // Order Frequency
                        min(100, ($avgOrderValue / $maxAvgOrder) * 100),   // Order Value
                        min(100, $profitMargin),                           // Profit Margin
                        min(100, $efficiency),                             // Efficiency
                    ],
                    'backgroundColor' => 'rgba(34, 197, 94, 0.2)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 2,
                    'pointBackgroundColor' => 'rgb(34, 197, 94)',
                    'pointBorderColor' => '#fff',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 4,
                ],
            ],
            'labels' => [
                __('Sales Volume'),
                __('Cost Control'),
                __('Order Frequency'),
                __('Order Value'),
                __('Profit Margin'),
                __('Efficiency'),
            ],
        ];
    }

    protected function getType(): string
    {
        return 'radar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'r' => [
                    'beginAtZero' => true,
                    'max' => 100,
                    'ticks' => [
                        'stepSize' => 20,
                    ],
                ],
            ],
            'maintainAspectRatio' => false,
        ];
    }

    public function getDescription(): ?string
    {
        return __('Radar chart showing 6 key business metrics normalized to 0-100%. The larger the area, the better the overall performance. Each axis represents: Sales Volume (total sales), Cost Control (expense efficiency), Order Frequency (number of orders), Order Value (average per order), Profit Margin (profitability %), and Efficiency (sales/expense ratio).');
    }

    public static function canView(): bool
    {
        return true; // Mostrar en dashboard principal y estadísticas
    }
}
