<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Carbon\Carbon;
use Filament\Support\Colors\Color;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatisticsSalesWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $last30Days = Carbon::now()->subDays(30);
        $last60Days = Carbon::now()->subDays(60);
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        // Ventas últimos 30 días
        $sales30Days = Order::where('created_at', '>=', $last30Days)->sum('total');
        $salesPrevious30Days = Order::whereBetween('created_at', [$last60Days, $last30Days])->sum('total');
        $salesChange = $salesPrevious30Days > 0 ?
            (($sales30Days - $salesPrevious30Days) / $salesPrevious30Days) * 100 : 0;

        // Ventas mes actual
        $salesCurrentMonth = Order::where('created_at', '>=', $currentMonth)->sum('total');
        $salesLastMonth = Order::whereBetween('created_at', [$lastMonth, $currentMonth])->sum('total');
        $salesMonthChange = $salesLastMonth > 0 ?
            (($salesCurrentMonth - $salesLastMonth) / $salesLastMonth) * 100 : 0;

        // Promedio por orden
        $avgPerOrder = Order::where('created_at', '>=', $last30Days)->avg('total') ?? 0;

        // Total de órdenes
        $totalOrders = Order::where('created_at', '>=', $last30Days)->count();
        $previousOrders = Order::whereBetween('created_at', [$last60Days, $last30Days])->count();
        $ordersChange = $previousOrders > 0 ?
            (($totalOrders - $previousOrders) / $previousOrders) * 100 : 0;

        return [
            Stat::make(__('Total Sales').' (30d)', 'L'.number_format($sales30Days, 2))
                ->description(round(abs($salesChange), 2).'% '.($salesChange >= 0 ? __('increase') : __('decrease')).' '.__('from previous 30 days'))
                ->descriptionIcon($salesChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($salesChange >= 0 ? Color::Green : Color::Red),

            Stat::make(__('Monthly Sales'), 'L'.number_format($salesCurrentMonth, 2))
                ->description(round(abs($salesMonthChange), 2).'% '.($salesMonthChange >= 0 ? __('increase') : __('decrease')).' '.__('from last month'))
                ->descriptionIcon($salesMonthChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($salesMonthChange >= 0 ? Color::Green : Color::Red),

            Stat::make(__('Average per Order'), 'L'.number_format($avgPerOrder, 2))
                ->description(__('Average order value'))
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color(Color::Blue),

            Stat::make(__('Total Orders').' (30d)', $totalOrders)
                ->description(abs($ordersChange).'% '.($ordersChange >= 0 ? __('increase') : __('decrease')).' '.__('from previous 30 days'))
                ->descriptionIcon($ordersChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($ordersChange >= 0 ? Color::Green : Color::Red),
        ];
    }

    protected function getColumns(): int
    {
        return 4;
    }

    public function getDisplayName(): string
    {
        return __('Sales Metrics');
    }
}
