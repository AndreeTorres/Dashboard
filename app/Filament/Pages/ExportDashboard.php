<?php

namespace App\Filament\Pages;

use App\Exports\StatisticsExport;
use App\Exports\TopProductsExport;
use App\Exports\ExpensesAnalysisExport;
use App\Models\Order;
use App\Models\Expense;
use App\Models\OrderDetail;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ExportDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-down';
    
    protected static ?string $navigationLabel = 'Exportar Datos';
    
    protected static ?string $navigationGroup = 'Reportes';

    protected static string $view = 'filament.pages.export-dashboard';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_all_statistics')
                ->label('Exportar Todas las Estadísticas')
                ->icon('heroicon-o-chart-bar')
                ->color('success')
                ->action(function () {
                    $stats = $this->getAllStatistics();
                    $filename = 'estadisticas-completas-' . now()->format('Y-m-d-H-i') . '.xlsx';
                    
                    return Excel::download(new StatisticsExport($stats), $filename);
                }),
                
            Action::make('export_daily_report')
                ->label('Reporte Diario')
                ->icon('heroicon-o-calendar-days')
                ->color('info')
                ->action(function () {
                    $dailyData = $this->getDailyReport();
                    $filename = 'reporte-diario-' . now()->format('Y-m-d') . '.xlsx';
                    
                    return Excel::download(new StatisticsExport($dailyData), $filename);
                }),
                
            Action::make('export_monthly_report')
                ->label('Reporte Mensual')
                ->icon('heroicon-o-calendar')
                ->color('warning')
                ->action(function () {
                    $monthlyData = $this->getMonthlyReport();
                    $filename = 'reporte-mensual-' . now()->format('Y-m') . '.xlsx';
                    
                    return Excel::download(new StatisticsExport($monthlyData), $filename);
                }),
        ];
    }

    protected function getAllStatistics(): array
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth();
        
        // Estadísticas generales
        $totalOrders = Order::count();
        $totalRevenue = Order::sum('total');
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        
        // Estadísticas de hoy
        $todayOrders = Order::whereDate('created_at', $today)->count();
        $todayRevenue = Order::whereDate('created_at', $today)->sum('total');
        
        // Estadísticas del mes
        $thisMonthOrders = Order::where('created_at', '>=', $thisMonth)->count();
        $thisMonthRevenue = Order::where('created_at', '>=', $thisMonth)->sum('total');
        
        // Estadísticas de gastos
        $totalExpenses = Expense::sum('amount');
        $thisMonthExpenses = Expense::where('created_at', '>=', $thisMonth)->sum('amount');
        
        // Productos más vendidos
        $topProducts = OrderDetail::select('product_id')
            ->selectRaw('SUM(quantity) as total_sold')
            ->selectRaw('SUM(quantity * unit_price) as total_revenue')
            ->with('product')
            ->groupBy('product_id')
            ->orderBy('total_sold', 'desc')
            ->limit(10)
            ->get();

        $data = [
            ['ESTADÍSTICAS GENERALES', ''],
            ['Métrica', 'Valor'],
            ['Total de Órdenes', $totalOrders],
            ['Ingresos Totales (HNL)', number_format($totalRevenue, 2)],
            ['Valor Promedio por Orden (HNL)', number_format($averageOrderValue, 2)],
            ['', ''],
            ['ESTADÍSTICAS DE HOY', ''],
            ['Órdenes Hoy', $todayOrders],
            ['Ingresos Hoy (HNL)', number_format($todayRevenue, 2)],
            ['', ''],
            ['ESTADÍSTICAS DEL MES', ''],
            ['Órdenes Este Mes', $thisMonthOrders],
            ['Ingresos Este Mes (HNL)', number_format($thisMonthRevenue, 2)],
            ['Gastos Este Mes (HNL)', number_format($thisMonthExpenses, 2)],
            ['Ganancia Neta Este Mes (HNL)', number_format($thisMonthRevenue - $thisMonthExpenses, 2)],
            ['', ''],
            ['TOP 10 PRODUCTOS MÁS VENDIDOS', ''],
            ['Producto', 'Cantidad Vendida'],
        ];

        foreach ($topProducts as $product) {
            $data[] = [
                $product->product->name ?? 'Producto no encontrado',
                $product->total_sold
            ];
        }

        return $data;
    }

    protected function getDailyReport(): array
    {
        $today = Carbon::today();
        
        $todayOrders = Order::where('created_at', '>=', $today->startOfDay())
                           ->where('created_at', '<=', $today->endOfDay())
                           ->get();
        $todayExpenses = Expense::where('created_at', '>=', $today->startOfDay())
                               ->where('created_at', '<=', $today->endOfDay())
                               ->get();
        
        $data = [
            ['REPORTE DIARIO - ' . $today->format('d/m/Y'), ''],
            ['', ''],
            ['RESUMEN', ''],
            ['Total Órdenes', $todayOrders->count()],
            ['Ingresos Totales (HNL)', number_format($todayOrders->sum('total'), 2)],
            ['Gastos Totales (HNL)', number_format($todayExpenses->sum('amount'), 2)],
            ['Ganancia Neta (HNL)', number_format($todayOrders->sum('total') - $todayExpenses->sum('amount'), 2)],
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

        if ($todayExpenses->count() > 0) {
            $data[] = ['', ''];
            $data[] = ['DETALLE DE GASTOS', ''];
            $data[] = ['Descripción', 'Monto', 'Hora'];
            
            foreach ($todayExpenses as $expense) {
                $data[] = [
                    $expense->description ?? 'N/A',
                    number_format($expense->amount, 2),
                    $expense->created_at->format('H:i')
                ];
            }
        }

        return $data;
    }

    protected function getMonthlyReport(): array
    {
        $thisMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        $monthlyOrders = Order::whereBetween('created_at', [$thisMonth, $endOfMonth])->get();
        $monthlyExpenses = Expense::whereBetween('created_at', [$thisMonth, $endOfMonth])->get();
        
        // Ventas por día
        $dailySales = Order::whereBetween('created_at', [$thisMonth, $endOfMonth])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as orders_count, SUM(total) as daily_total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $data = [
            ['REPORTE MENSUAL - ' . $thisMonth->format('m/Y'), ''],
            ['', ''],
            ['RESUMEN DEL MES', ''],
            ['Total Órdenes', $monthlyOrders->count()],
            ['Ingresos Totales (HNL)', number_format($monthlyOrders->sum('total'), 2)],
            ['Gastos Totales (HNL)', number_format($monthlyExpenses->sum('amount'), 2)],
            ['Ganancia Neta (HNL)', number_format($monthlyOrders->sum('total') - $monthlyExpenses->sum('amount'), 2)],
            ['Promedio de Ventas por Día (HNL)', number_format($monthlyOrders->sum('total') / max(1, $dailySales->count()), 2)],
            ['', ''],
            ['VENTAS POR DÍA', ''],
            ['Fecha', 'Órdenes', 'Total (HNL)'],
        ];

        foreach ($dailySales as $day) {
            $data[] = [
                Carbon::parse($day->date)->format('d/m/Y'),
                $day->orders_count,
                number_format($day->daily_total, 2)
            ];
        }

        return $data;
    }
}
