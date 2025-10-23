<?php

namespace App\Filament\Resources\ExpenseResource\Widgets;

use App\Models\Expense;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ExpenseChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Gastos por Categoría';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        // Obtener gastos por categoría de insumo en los últimos 6 meses
        $expenses = Expense::with(['supply.supplyCategory'])
            ->where('date', '>=', Carbon::now()->subMonths(6))
            ->get()
            ->groupBy('supply.supplyCategory.name')
            ->map(function ($categoryExpenses) {
                return $categoryExpenses->sum('cost');
            })
            ->sortDesc()
            ->take(8); // Top 8 categorías

        return [
            'datasets' => [
                [
                    'label' => __('Expense Amount'),
                    'data' => $expenses->values()->toArray(),
                    'backgroundColor' => [
                        'rgb(59, 130, 246)',   // blue
                        'rgb(16, 185, 129)',   // green
                        'rgb(245, 158, 11)',   // yellow
                        'rgb(239, 68, 68)',    // red
                        'rgb(139, 92, 246)',   // purple
                        'rgb(236, 72, 153)',   // pink
                        'rgb(6, 182, 212)',    // cyan
                        'rgb(34, 197, 94)',    // emerald
                    ],
                    'borderColor' => [
                        'rgb(29, 78, 216)',
                        'rgb(5, 150, 105)',
                        'rgb(217, 119, 6)',
                        'rgb(220, 38, 38)',
                        'rgb(124, 58, 237)',
                        'rgb(219, 39, 119)',
                        'rgb(8, 145, 178)',
                        'rgb(22, 163, 74)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $expenses->keys()->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
            'maintainAspectRatio' => false,
        ];
    }
}
