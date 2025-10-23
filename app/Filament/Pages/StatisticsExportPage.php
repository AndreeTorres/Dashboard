<?php

namespace App\Filament\Pages;

use App\Exports\StatisticsExport as StatsExport;
use App\Models\Order;
use App\Models\Expense;
use App\Models\OrderDetail;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class StatisticsExportPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    
    protected static ?string $navigationLabel = 'Exportar Estadísticas';
    
    protected static ?string $navigationGroup = 'Reportes';

    protected static string $view = 'filament.pages.statistics-export';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_general_stats')
                ->label('Exportar Estadísticas Generales')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    $stats = $this->getGeneralStatistics();
                    $filename = 'estadisticas-generales-' . now()->format('Y-m-d-H-i') . '.xlsx';
                    
                    return Excel::download(new StatsExport($stats), $filename);
                }),
                
            Action::make('export_top_products')
                ->label('Exportar Top Productos')
                ->icon('heroicon-o-star')
                ->color('warning')
                ->action(function () {
                    $topProducts = $this->getTopProductsData();
                    $filename = 'top-productos-' . now()->format('Y-m-d') . '.xlsx';
                    
                    return Excel::download(new StatsExport($topProducts), $filename);
                }),
                
            Action::make('export_daily_report')
                ->label('Exportar Reporte Diario')
                ->icon('heroicon-o-calendar-days')
                ->color('info')
                ->action(function () {
                    $dailyData = $this->getDailyReport();
                    $filename = 'reporte-diario-' . now()->format('Y-m-d') . '.xlsx';
                    
                    return Excel::download(new StatsExport($dailyData), $filename);
                }),
        ];
    }

    protected function getGeneralStatistics(): array
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        
        $totalOrders = Order::count();
        $totalRevenue = Order::sum('total');
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        
        $todayOrders = Order::whereDate('created_at', $today)->count();
        $todayRevenue = Order::whereDate('created_at', $today)->sum('total');
        
        $thisMonthOrders = Order::where('created_at', '>=', $thisMonth)->count();
        $thisMonthRevenue = Order::where('created_at', '>=', $thisMonth)->sum('total');
        
        $totalExpenses = Expense::sum('amount');
        $thisMonthExpenses = Expense::where('created_at', '>=', $thisMonth)->sum('amount');
        
        return [
            ['Métrica', 'Valor'],
            ['Total de Órdenes', $totalOrders],
            ['Ingresos Totales (HNL)', number_format((float) $totalRevenue, 2)],
            ['Valor Promedio por Orden (HNL)', number_format($averageOrderValue, 2)],
            ['Órdenes Hoy', $todayOrders],
            ['Ingresos Hoy (HNL)', number_format((float) $todayRevenue, 2)],
            ['Órdenes Este Mes', $thisMonthOrders],
            ['Ingresos Este Mes (HNL)', number_format((float) $thisMonthRevenue, 2)],
            ['Gastos Totales (HNL)', number_format((float) $totalExpenses, 2)],
            ['Gastos Este Mes (HNL)', number_format((float) $thisMonthExpenses, 2)],
            ['Ganancia Neta Este Mes (HNL)', number_format((float) ($thisMonthRevenue - $thisMonthExpenses), 2)],
        ];
    }

    protected function getTopProductsData(): array
    {
        $topProducts = OrderDetail::select('product_id')
            ->selectRaw('SUM(quantity) as total_sold')
            ->selectRaw('SUM(quantity * unit_price) as total_revenue')
            ->with('product')
            ->groupBy('product_id')
            ->orderBy('total_sold', 'desc')
            ->limit(20)
            ->get();

        $data = [['Producto', 'Cantidad Vendida', 'Ingresos (HNL)']];
        
        foreach ($topProducts as $item) {
            $data[] = [
                $item->product->name ?? 'Producto no encontrado',
                $item->total_sold,
                number_format((float) $item->total_revenue, 2)
            ];
        }

        return $data;
    }

    protected function getDailyReport(): array
    {
        $today = Carbon::today();
        
        $todayOrders = Order::where('created_at', '>=', $today->startOfDay())
                           ->where('created_at', '<=', $today->copy()->endOfDay())
                           ->get();
        $todayExpenses = Expense::where('created_at', '>=', $today->startOfDay())
                               ->where('created_at', '<=', $today->copy()->endOfDay())
                               ->get();
        
        $data = [
            ['REPORTE DIARIO - ' . $today->format('d/m/Y'), ''],
            ['', ''],
            ['RESUMEN', ''],
            ['Total Órdenes', $todayOrders->count()],
            ['Ingresos Totales (HNL)', number_format((float) $todayOrders->sum('total'), 2)],
            ['Gastos Totales (HNL)', number_format((float) $todayExpenses->sum('amount'), 2)],
            ['Ganancia Neta (HNL)', number_format((float) ($todayOrders->sum('total') - $todayExpenses->sum('amount')), 2)],
            ['', ''],
            ['DETALLE DE ÓRDENES', ''],
            ['ID Orden', 'Usuario', 'Total', 'Estado', 'Hora'],
        ];

        foreach ($todayOrders as $order) {
            $data[] = [
                $order->identifier,
                $order->user->name ?? 'N/A',
                number_format((float) $order->total, 2),
                $order->status,
                $order->created_at->format('H:i')
            ];
        }

        return $data;
    }
}
