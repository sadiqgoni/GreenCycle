<?php

namespace App\Filament\Company\Widgets;

use App\Models\ServiceRequest;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class CompanyRevenueChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'companyRevenueChart';

    protected static ?int $sort = 3;
    public function getColumnSpan(): int|string|array
    {
        return 'full'; // Makes the chart span the full width of the layout
    }
    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Revenue Overview';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        $userId = auth()->id();

        // Fetch revenue data grouped by day
        $revenueData = ServiceRequest::where('accepted_company_id', $userId)
        ->whereNotNull('company_payout_amount') // Ensure only completed or paid requests are considered
        ->selectRaw('DATE_FORMAT(updated_at, "%Y-%m") as month, SUM(company_payout_amount) as total_revenue')
        ->groupBy('month')
        ->orderBy('month')
        ->pluck('total_revenue', 'month');

        return [
            'chart' => [
                'type' => 'area',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'Revenue',
                    'data' => $revenueData->values()->toArray(),
                ],
            ],
            'xaxis' => [
                'categories' => $revenueData->keys()->toArray(),
                'title' => [
                    'text' => 'Daily Revenue',
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
