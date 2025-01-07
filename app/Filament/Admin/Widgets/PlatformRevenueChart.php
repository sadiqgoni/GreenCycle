<?php

namespace App\Filament\Admin\Widgets;

use App\Models\ServiceRequest;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class PlatformRevenueChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'platformRevenueChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'PlatformRevenueChart';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected static ?int $sort = 4;

    protected function getOptions(): array
    {
            // Fetch admin revenue grouped by day
            $revenueData = ServiceRequest::whereNotNull('admin_commission_amount')
            ->selectRaw('DATE_FORMAT(updated_at, "%Y-%m-%d") as day, SUM(admin_commission_amount) as total_revenue')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total_revenue', 'day');

        return [
            'chart' => [
                'type' => 'area',
                'height' => 300,
            ],
            'series' => [
                [
                    [
                        'name' => 'Revenue',
                        'data' => $revenueData->values()->toArray(),
                    ],
                ],
            ],
            'xaxis' => [
                'categories' => $revenueData->keys()->toArray(),
                'title' => [
                    'text' => 'Date',
                ],
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'title' => [
                    'text' => 'Revenue (₦)',
                ],
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => ['#f59e0b'],
            'stroke' => [
                'curve' => 'smooth',
            ],
            'dataLabels' => [
                'enabled' => false,
            ],
        ];
    }
}
