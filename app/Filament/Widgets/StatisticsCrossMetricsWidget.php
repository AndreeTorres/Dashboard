<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use App\Models\Order;
use Carbon\Carbon;
use Filament\Support\Colors\Color;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatisticsCrossMetricsWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        $last30Days = Carbon::now()->subDays(30);
        $last60Days = Carbon::now()->subDays(60);

        // Datos últimos 30 días
        $sales30Days = Order::where('created_at', '>=', $last30Days)->sum('total');
        $expenses30Days = Expense::where('date', '>=', $last30Days)->sum('cost');

        // Datos 30 días anteriores
        $salesPrevious30Days = Order::whereBetween('created_at', [$last60Days, $last30Days])->sum('total');
        $expensesPrevious30Days = Expense::whereBetween('date', [$last60Days, $last30Days])->sum('cost');

        // Cálculos de métricas cruzadas
        $netProfit = $sales30Days - $expenses30Days;
        $previousNetProfit = $salesPrevious30Days - $expensesPrevious30Days;
        $netProfitChange = $previousNetProfit != 0 ? (($netProfit - $previousNetProfit) / abs($previousNetProfit)) * 100 : 0;

        // Margen de ganancia (Sales - Expenses) / Sales * 100
        $profitMargin = $sales30Days > 0 ? (($sales30Days - $expenses30Days) / $sales30Days) * 100 : 0;
        $previousProfitMargin = $salesPrevious30Days > 0 ? (($salesPrevious30Days - $expensesPrevious30Days) / $salesPrevious30Days) * 100 : 0;
        $profitMarginChange = $previousProfitMargin != 0 ? $profitMargin - $previousProfitMargin : 0;

        // ROI (Return on Investment) = (Sales - Expenses) / Expenses * 100
        $roi = $expenses30Days > 0 ? (($sales30Days - $expenses30Days) / $expenses30Days) * 100 : 0;
        $previousRoi = $expensesPrevious30Days > 0 ? (($salesPrevious30Days - $expensesPrevious30Days) / $expensesPrevious30Days) * 100 : 0;
        $roiChange = $previousRoi != 0 ? $roi - $previousRoi : 0;

        // Ratio de gastos = Expenses / Sales * 100
        $expenseRatio = $sales30Days > 0 ? ($expenses30Days / $sales30Days) * 100 : 0;
        $previousExpenseRatio = $salesPrevious30Days > 0 ? ($expensesPrevious30Days / $salesPrevious30Days) * 100 : 0;
        $expenseRatioChange = $previousExpenseRatio != 0 ? $expenseRatio - $previousExpenseRatio : 0;

        return [
            Stat::make(__('Net Profit').' (30d)', 'L'.number_format($netProfit, 2))
                ->description(round(abs($netProfitChange), 2).'% '.($netProfitChange >= 0 ? __('increase') : __('decrease')).' '.__('from previous 30 days'))
                ->descriptionIcon($netProfitChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($netProfit >= 0 ? Color::Green : Color::Red),

            Stat::make(__('Profit Margin'), round($profitMargin, 1).'%')
                ->description(round(abs($profitMarginChange), 1).'% '.($profitMarginChange >= 0 ? __('increase') : __('decrease')).' '.__('from previous 30 days'))
                ->descriptionIcon($profitMarginChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($profitMargin >= 0 ? Color::Green : Color::Red),

            Stat::make(__('ROI'), round($roi, 1).'%')
                ->description(round(abs($roiChange), 1).'% '.($roiChange >= 0 ? __('increase') : __('decrease')).' '.__('from previous 30 days'))
                ->descriptionIcon($roiChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($roi >= 0 ? Color::Green : Color::Red),

            Stat::make(__('Expense Ratio'), round($expenseRatio, 1).'%')
                ->description(round(abs($expenseRatioChange), 1).'% '.($expenseRatioChange >= 0 ? __('increase') : __('decrease')).' '.__('from previous 30 days'))
                ->descriptionIcon($expenseRatioChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($expenseRatioChange <= 0 ? Color::Green : Color::Red), // Menos gastos es mejor
        ];
    }

    protected function getColumns(): int
    {
        return 4;
    }

    public function getDisplayName(): string
    {
        return __('Cross Metrics');
    }

    public static function canView(): bool
    {
        return true; // Mostrar en dashboard principal y estadísticas
    }
}
