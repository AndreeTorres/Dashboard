<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use Carbon\Carbon;
use Filament\Support\Colors\Color;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatisticsExpensesWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $last30Days = Carbon::now()->subDays(30);
        $last60Days = Carbon::now()->subDays(60);
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        // Gastos últimos 30 días
        $expenses30Days = Expense::where('date', '>=', $last30Days)->sum('cost');
        $expensesPrevious30Days = Expense::whereBetween('date', [$last60Days, $last30Days])->sum('cost');
        $expensesChange = $expensesPrevious30Days > 0 ?
            (($expenses30Days - $expensesPrevious30Days) / $expensesPrevious30Days) * 100 : 0;

        // Gastos mes actual
        $expensesCurrentMonth = Expense::where('date', '>=', $currentMonth)->sum('cost');
        $expensesLastMonth = Expense::whereBetween('date', [$lastMonth, $currentMonth])->sum('cost');
        $expensesMonthChange = $expensesLastMonth > 0 ?
            (($expensesCurrentMonth - $expensesLastMonth) / $expensesLastMonth) * 100 : 0;

        // Promedio diario
        $avgDaily = Expense::where('date', '>=', $last30Days)->count() > 0 ?
            $expenses30Days / 30 : 0;

        return [
            Stat::make(__('Total Expenses').' (30d)', 'L'.number_format($expenses30Days, 2))
                ->description(round(abs($expensesChange), 2).'% '.($expensesChange >= 0 ? __('increase') : __('decrease')).' '.__('from previous 30 days'))
                ->descriptionIcon($expensesChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($expensesChange >= 0 ? Color::Red : Color::Green),

            Stat::make(__('Monthly Expenses'), 'L'.number_format($expensesCurrentMonth, 2))
                ->description(round(abs($expensesMonthChange), 2).'% '.($expensesMonthChange >= 0 ? __('increase') : __('decrease')).' '.__('from last month'))
                ->descriptionIcon($expensesMonthChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($expensesMonthChange >= 0 ? Color::Red : Color::Green),

            Stat::make(__('Daily Average').' (30d)', 'L'.number_format($avgDaily, 2))
                ->description(__('Average daily expenses'))
                ->descriptionIcon('heroicon-m-calculator')
                ->color(Color::Blue),

            Stat::make(__('Records').' (30d)', Expense::where('date', '>=', $last30Days)->count())
                ->description(__('Total expense records'))
                ->descriptionIcon('heroicon-m-document-text')
                ->color(Color::Gray),
        ];
    }

    protected function getColumns(): int
    {
        return 4;
    }

    public function getDisplayName(): string
    {
        return __('Expenses Metrics');
    }
}
