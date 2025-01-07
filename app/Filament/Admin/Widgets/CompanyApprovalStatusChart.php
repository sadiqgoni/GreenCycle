<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Company;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class CompanyApprovalStatusChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?int $sort = 4;

    protected static ?string $chartId = 'companyApprovalStatusChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'CompanyApprovalStatusChart';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
          // Fetch counts of companies grouped by their verification status
          $companyStatuses = Company::selectRaw('verification_status, COUNT(*) as count')
          ->groupBy('verification_status')
          ->pluck('count', 'verification_status');

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'Companies',
                    'data' => $companyStatuses->values()->toArray(),
                ],
            ],
            'xaxis' => [
                'categories' => $companyStatuses->keys()->map(function ($status) {
                    return ucfirst($status); // Capitalize statuses (e.g., 'verified')
                })->toArray(),
                'title' => [
                    'text' => 'Verification Status',
                ],
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'title' => [
                    'text' => 'Number of Companies',
                ],
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => ['#f59e0b','#28a745', '#ffc107', '#dc3545'],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 3,
                    'horizontal' => false,
                ],
            ],
        ];
    }
}
