<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class MonthlyComparisonChart extends ChartWidget
{
    protected static ?string $heading = null;

    public function getHeading(): string
    {
        return __('Monthly Income vs Expenses');
    }

    protected static ?int $sort = 5;

    protected static ?string $maxHeight = '400px';

    protected function getData(): array
    {
        $months = collect();
        $salesData = [];
        $expensesData = [];
        $labels = [];

        // Obtener datos de los últimos 6 meses
        for ($i = 5; $i >= 0; $i--) {
            $startOfMonth = Carbon::now()->subMonths($i)->startOfMonth();
            $endOfMonth = Carbon::now()->subMonths($i)->endOfMonth();

            $labels[] = $startOfMonth->format('M Y');

            // Ventas del mes
            $monthlySales = Order::whereBetween('created_at', [$startOfMonth, $endOfMonth])->sum('total');
            $salesData[] = $monthlySales;

            // Gastos del mes
            $monthlyExpenses = Expense::whereBetween('date', [$startOfMonth, $endOfMonth])->sum('cost');
            $expensesData[] = $monthlyExpenses;
        }

        return [
            'datasets' => [
                [
                    'label' => __('Sales'),
                    'data' => $salesData,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => __('Expenses'),
                    'data' => $expensesData,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
                    'borderColor' => 'rgb(239, 68, 68)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
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
                'x' => [
                    'categoryPercentage' => 0.8,
                    'barPercentage' => 0.9,
                ],
            ],
            'interaction' => [
                'mode' => 'nearest',
                'intersect' => false,
            ],
            'maintainAspectRatio' => false,
        ];
    }

    public function getDescription(): ?string
    {
        return __('6-month monthly comparison of income and expenses');
    }

    public static function canView(): bool
    {
        return true; // Mostrar en dashboard principal y estadísticas
    }
}
