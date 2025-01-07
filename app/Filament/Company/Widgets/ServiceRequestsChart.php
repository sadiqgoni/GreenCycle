<?php

namespace App\Filament\Company\Widgets;

use App\Models\ServiceRequest;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ServiceRequestsChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'serviceRequestsChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'ServiceRequestsChart';
    protected static ?int $sort = 2;
    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        $userId = auth()->id();
        // Get the count of service requests grouped by status
        $statuses = ServiceRequest::where('accepted_company_id', $userId)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'Service Requests',
                    'data' => $statuses->values()->toArray(),
                ],
            ],
            'xaxis' => [
                'categories' => $statuses->keys()->toArray(),
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => ['#f59e0b'],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 3,
                    'horizontal' => false,
                ],
            ],
        ];
    }
}
