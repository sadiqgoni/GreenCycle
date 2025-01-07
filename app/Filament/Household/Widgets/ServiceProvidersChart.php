<?php

namespace App\Filament\Household\Widgets;

use App\Models\ServiceRequest;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ServiceProvidersChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'serviceProvidersChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'ServiceProvidersChart';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        $userId = auth()->id();

        // Fetch data for top service providers by spending
        $providersData = ServiceRequest::where('household_id', $userId)
            ->whereNotNull('final_amount')
            ->join('companies', 'companies.id', '=', 'service_requests.accepted_company_id')
            ->selectRaw('companies.company_name, SUM(service_requests.final_amount) as total_spent')
            ->groupBy('companies.company_name')
            ->orderByDesc('total_spent')
            ->limit(5)
            ->pluck('total_spent', 'company_name');
        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'Total Spent',
                    'data' => $providersData->values()->toArray(),
                ],
            ],
            'xaxis' => [
                'categories' => $providersData->keys()->toArray(),
                'title' => [
                    'text' => 'Service Providers',
                ],
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'title' => [
                    'text' => 'Amount (₦)',
                ],
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
