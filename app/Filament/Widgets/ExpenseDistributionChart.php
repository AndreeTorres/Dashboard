<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class ExpenseDistributionChart extends ChartWidget
{
    protected static ?string $heading = null;

    public function getHeading(): string
    {
        return __('Expense Distribution by Category');
    }

    protected static ?int $sort = 6;

    protected static ?string $maxHeight = '400px';

    protected function getData(): array
    {
        // Obtener gastos por categoría de suministro en los últimos 30 días
        $expenses = Expense::with(['supply.supplyCategory'])
            ->where('date', '>=', Carbon::now()->subDays(30))
            ->get()
            ->groupBy(function ($expense) {
                return $expense->supply->supplyCategory->name ?? __('Other');
            })
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
            'cutout' => '60%',
        ];
    }

    public function getDescription(): ?string
    {
        return __('Expense breakdown by supply category (last 30 days)');
    }

    public static function canView(): bool
    {
        return true; // Mostrar en dashboard principal y estadísticas
    }
}
