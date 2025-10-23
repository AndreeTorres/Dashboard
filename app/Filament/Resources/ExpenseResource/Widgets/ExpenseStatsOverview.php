<?php

namespace App\Filament\Resources\ExpenseResource\Widgets;

use App\Models\Expense;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class ExpenseStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Gastos de los últimos 30 días
        $last30DaysExpenses = Expense::where('date', '>=', Carbon::now()->subDays(30))
            ->sum('cost');

        // Gastos de los 30 días anteriores (días 31-60)
        $previous30DaysExpenses = Expense::where('date', '>=', Carbon::now()->subDays(60))
            ->where('date', '<', Carbon::now()->subDays(30))
            ->sum('cost');

        // Calcular incremento/decremento porcentual
        $periodChange = $previous30DaysExpenses > 0
            ? (($last30DaysExpenses - $previous30DaysExpenses) / $previous30DaysExpenses) * 100
            : 0;

        // Promedio de gastos diarios de los últimos 30 días
        $avgDailyExpense = $last30DaysExpenses / 30;

        // Número total de gastos de los últimos 30 días
        $last30DaysCount = Expense::where('date', '>=', Carbon::now()->subDays(30))
            ->count();

        return [
            Stat::make(__('Last 30 Days'), 'L'.number_format($last30DaysExpenses, 2))
                ->description($periodChange >= 0
                    ? '+'.number_format($periodChange, 1).'% '.__('from previous 30 days')
                    : number_format($periodChange, 1).'% '.__('from previous 30 days'))
                ->descriptionIcon($periodChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($periodChange >= 0 ? 'warning' : 'success'),

            Stat::make(__('Daily Average'), 'L'.number_format($avgDailyExpense, 2))
                ->description(__('Average per day last 30 days'))
                ->descriptionIcon('heroicon-m-calculator')
                ->color('info'),

            Stat::make(__('Records (30 days)'), number_format($last30DaysCount))
                ->description(__('Expense records last 30 days'))
                ->descriptionIcon('heroicon-m-document-text')
                ->color('gray'),
        ];
    }
}
