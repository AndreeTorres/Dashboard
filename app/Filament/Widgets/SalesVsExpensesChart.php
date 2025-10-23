<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class SalesVsExpensesChart extends ChartWidget
{
    protected static ?string $heading = null;

    public function getHeading(): string
    {
        return __('Sales vs Expenses Trend');
    }

    protected static ?int $sort = 4;

    protected static ?string $maxHeight = '400px';

    protected function getData(): array
    {
        // Obtener datos de los últimos 30 días
        $days = collect();
        $salesData = [];
        $expensesData = [];
        $labels = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('M j');

            // Ventas del día
            $dailySales = Order::whereDate('created_at', $date)->sum('total');
            $salesData[] = $dailySales;

            // Gastos del día
            $dailyExpenses = Expense::whereDate('date', $date)->sum('cost');
            $expensesData[] = $dailyExpenses;
        }

        return [
            'datasets' => [
                [
                    'label' => __('Sales'),
                    'data' => $salesData,
                    'borderColor' => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => __('Expenses'),
                    'data' => $expensesData,
                    'borderColor' => 'rgb(239, 68, 68)',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
            'interaction' => [
                'mode' => 'nearest',
                'axis' => 'x',
                'intersect' => false,
            ],
            'maintainAspectRatio' => false,
        ];
    }

    public function getDescription(): ?string
    {
        return __('30-day sales vs expenses trend comparison');
    }

    public static function canView(): bool
    {
        return true; // Mostrar en dashboard principal y estadísticas
    }
}
