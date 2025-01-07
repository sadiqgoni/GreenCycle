<?php

namespace App\Filament\Household\Widgets;

use App\Models\ServiceRequest;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ServiceRequestTrendsChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'serviceRequestTrendsChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'ServiceRequestTrendsChart';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        $userId = auth()->id();

        // Fetch service request trends grouped by day
        $trendsData = ServiceRequest::where('household_id', $userId)
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m-%d") as day, COUNT(*) as total_requests')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total_requests', 'day');
        return [
            'chart' => [
                'type' => 'line',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'Requests',
                    'data' => $trendsData->values()->toArray(),
                ],
            ],
            'xaxis' => [
                'categories' => $trendsData->keys()->toArray(),
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
                    'text' => 'Number of Requests',
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
        ];
    }
}
