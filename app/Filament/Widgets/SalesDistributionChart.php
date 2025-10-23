<?php

namespace App\Filament\Widgets;

use App\Models\OrderDetail;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class SalesDistributionChart extends ChartWidget
{
    protected static ?string $heading = null;

    public function getHeading(): string
    {
        return __('Sales Distribution by Category');
    }

    protected static ?int $sort = 8;

    protected static ?string $maxHeight = '400px';

    protected function getData(): array
    {
        // Obtener ventas por categoría en los últimos 30 días
        $salesByCategory = OrderDetail::with(['product.subcategory.category'])
            ->whereHas('order', function ($query) {
                $query->where('created_at', '>=', Carbon::now()->subDays(30));
            })
            ->get()
            ->groupBy(function ($orderDetail) {
                return $orderDetail->product->subcategory->category->name ?? __('Other');
            })
            ->map(function ($categoryDetails) {
                return $categoryDetails->sum(function ($detail) {
                    return $detail->quantity * $detail->unit_price;
                });
            })
            ->sortDesc()
            ->take(8); // Top 8 categorías

        return [
            'datasets' => [
                [
                    'label' => __('Sales Amount'),
                    'data' => $salesByCategory->values()->toArray(),
                    'backgroundColor' => [
                        'rgb(34, 197, 94)',    // green
                        'rgb(59, 130, 246)',   // blue
                        'rgb(245, 158, 11)',   // yellow
                        'rgb(139, 92, 246)',   // purple
                        'rgb(236, 72, 153)',   // pink
                        'rgb(6, 182, 212)',    // cyan
                        'rgb(16, 185, 129)',   // emerald
                        'rgb(239, 68, 68)',    // red
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
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $salesByCategory->keys()->toArray(),
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
        return __('Sales breakdown by product category (last 30 days)');
    }

    public static function canView(): bool
    {
        return true; // Mostrar en dashboard principal y estadísticas
    }
}
