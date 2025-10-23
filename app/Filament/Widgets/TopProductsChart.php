<?php

namespace App\Filament\Widgets;

use App\Models\OrderDetail;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class TopProductsChart extends ChartWidget
{
    protected static ?string $heading = null;

    public function getHeading(): string
    {
        return __('Top Selling Products');
    }

    protected static ?int $sort = 10;

    protected static ?string $maxHeight = '400px';

    protected function getData(): array
    {
        // Obtener productos más vendidos en los últimos 30 días
        $topProducts = OrderDetail::with(['product'])
            ->whereHas('order', function ($query) {
                $query->where('created_at', '>=', Carbon::now()->subDays(30));
            })
            ->get()
            ->groupBy('product.name')
            ->map(function ($productDetails) {
                return [
                    'total_sales' => $productDetails->sum(function ($detail) {
                        return $detail->quantity * $detail->unit_price;
                    }),
                    'total_quantity' => $productDetails->sum('quantity'),
                ];
            })
            ->sortByDesc('total_sales')
            ->take(8); // Top 8 productos

        return [
            'datasets' => [
                [
                    'label' => __('Sales Amount'),
                    'data' => $topProducts->map(fn ($item) => $item['total_sales'])->values()->toArray(),
                    'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => __('Quantity Sold'),
                    'data' => $topProducts->map(fn ($item) => $item['total_quantity'])->values()->toArray(),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $topProducts->keys()->map(function ($name) {
                return strlen($name) > 15 ? substr($name, 0, 15).'...' : $name;
            })->toArray(),
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
            ],
            'scales' => [
                'y' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'left',
                    'beginAtZero' => true,
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'beginAtZero' => true,
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                ],
                'x' => [
                    'ticks' => [
                        'maxRotation' => 45,
                    ],
                ],
            ],
            'maintainAspectRatio' => false,
        ];
    }

    public function getDescription(): ?string
    {
        return __('Top 8 best-selling products by revenue and quantity (last 30 days)');
    }

    public static function canView(): bool
    {
        return true; // Mostrar en dashboard principal y estadísticas
    }
}
