<?php

namespace App\Filament\Admin\Widgets;

use App\Models\ServiceRequest;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ServiceRequestsOverviewChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'serviceRequestsOverviewChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?int $sort = 3;

    protected static ?string $heading = 'ServiceRequestsOverviewChart';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
           // Fetch counts of service requests by status
           $statuses = ServiceRequest::selectRaw('status, COUNT(*) as count')
           ->groupBy('status')
           ->pluck('count', 'status');
        return [
            'chart' => [
                'type' => 'pie',
                'height' => 300,
            ],
            'series' => $statuses->values()->toArray(),
            'labels' => $statuses->keys()->toArray(),
            'legend' => [
                'labels' => [
                    'fontFamily' => 'inherit',
                ],
            ],
        ];
    }
}
