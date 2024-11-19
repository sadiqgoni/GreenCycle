<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Company;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class CompanyAvailabilityChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'companyAvailabilityChart';
    protected static ?int $sort = 1;


    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Company Availability Status';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        // Fetching the count of companies with availability_status as 'open' and 'closed'
        $data = Company::selectRaw('
            COUNT(CASE WHEN availability_status = "open" THEN 1 END) as open,
            COUNT(CASE WHEN availability_status = "closed" THEN 1 END) as closed
        ')->first();

        return [
            'chart' => [
                'type' => 'pie',
                'height' => 300,
            ],
            'series' => [
                $data->open ?? 0,
                $data->closed ?? 0,
            ],
            'labels' => ['Open', 'Closed'],
            'legend' => [
                'labels' => [
                    'fontFamily' => 'inherit',
                ],
            ],
            'colors' => ['#34d399', '#f87171'], 
            'tooltip' => [
                'y' => [
                    'formatter' => 'function (val) {
                        return val + " companies";
                    }',
                ],
            ],
        ];
    }
}
