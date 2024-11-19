<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Company;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class CompanyStatusChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'companyStatusChart';
    protected static ?int $sort = 2;


    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Company Request Status';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        // Fetching the count of pending, approved, and rejected requests
        $data = Company::selectRaw('
            COUNT(CASE WHEN verification_status = "pending" THEN 1 END) as pending,
            COUNT(CASE WHEN verification_status = "approved" THEN 1 END) as approved,
            COUNT(CASE WHEN verification_status = "rejected" THEN 1 END) as rejected
        ')->first();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'Requests',
                    'data' => [
                        $data->pending ?? 0,
                        $data->approved ?? 0,
                        $data->rejected ?? 0,
                    ],
                ],
            ],
            'xaxis' => [
                'categories' => ['Pending', 'Approved', 'Rejected'],
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
            'colors' => ['#f59e0b', '#10b981', '#ef4444'], // Yellow, Green, Red for each status
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 3,
                    'horizontal' => false,
                ],
            ],
            // 'tooltip' => [
            //     'y' => [
            //         'formatter' => function ($val) {
            //             return $val . " requests";
            //         },
            //     ],
            // ],
        ];
    }
}
