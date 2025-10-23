<?php

namespace App\Filament\Widgets;

use App\Models\OrderDetail;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class TopSubcategoriesChart extends ChartWidget
{
    protected static ?string $heading = null;

    public function getHeading(): string
    {
        return __('Top Selling Subcategories');
    }

    protected static ?int $sort = 9;

    protected static ?string $maxHeight = '400px';

    protected function getData(): array
    {
        // Obtener ventas por subcategoría en los últimos 30 días
        $salesBySubcategory = OrderDetail::with(['product.subcategory'])
            ->whereHas('order', function ($query) {
                $query->where('created_at', '>=', Carbon::now()->subDays(30));
            })
            ->get()
            ->groupBy(function ($orderDetail) {
                return $orderDetail->product->subcategory->name ?? __('Other');
            })
            ->map(function ($subcategoryDetails) {
                return $subcategoryDetails->sum(function ($detail) {
                    return $detail->quantity * $detail->unit_price;
                });
            })
            ->sortDesc()
            ->take(10); // Top 10 subcategorías

        return [
            'datasets' => [
                [
                    'label' => __('Sales Amount'),
                    'data' => $salesBySubcategory->values()->toArray(),
                    'backgroundColor' => [
                        'rgba(34, 197, 94, 0.8)',   // green
                        'rgba(59, 130, 246, 0.8)',  // blue
                        'rgba(245, 158, 11, 0.8)',  // yellow
                        'rgba(139, 92, 246, 0.8)',  // purple
                        'rgba(236, 72, 153, 0.8)',  // pink
                        'rgba(6, 182, 212, 0.8)',   // cyan
                        'rgba(16, 185, 129, 0.8)',  // emerald
                        'rgba(239, 68, 68, 0.8)',   // red
                        'rgba(156, 163, 175, 0.8)', // gray
                        'rgba(251, 146, 60, 0.8)',  // orange
                    ],
                    'borderColor' => [
                        'rgb(22, 163, 74)',
                        'rgb(29, 78, 216)',
                        'rgb(217, 119, 6)',
                        'rgb(124, 58, 237)',
                        'rgb(219, 39, 119)',
                        'rgb(8, 145, 178)',
                        'rgb(5, 150, 105)',
                        'rgb(220, 38, 38)',
                        'rgb(107, 114, 128)',
                        'rgb(234, 88, 12)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $salesBySubcategory->keys()->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y', // Barras horizontales
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                ],
                'y' => [
                    'ticks' => [
                        'maxTicksLimit' => 10,
                    ],
                ],
            ],
            'maintainAspectRatio' => false,
        ];
    }

    public function getDescription(): ?string
    {
        return __('Top 10 best-selling subcategories (last 30 days)');
    }

    public static function canView(): bool
    {
        return true; // Mostrar en dashboard principal y estadísticas
    }
}
