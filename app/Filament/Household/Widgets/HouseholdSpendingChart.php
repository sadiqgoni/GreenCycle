<?php

namespace App\Filament\Household\Widgets;

use App\Models\ServiceRequest;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class HouseholdSpendingChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'householdSpendingChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'HouseholdSpendingChart';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    public  function getColumnSpan(): string
    {
        return 'full'; // Full width on the dashboard
    }
    protected function getOptions(): array
    {
        $userId = auth()->id();

        // Fetch spending data grouped by day
        $spendingData = ServiceRequest::where('household_id', $userId)
            ->whereNotNull('final_amount')
            ->selectRaw('DATE_FORMAT(updated_at, "%Y-%m-%d") as day, SUM(final_amount) as total_spent')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total_spent', 'day');
        return [
            'chart' => [
                'type' => 'area',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'Spending',
                    'data' => $spendingData->values()->toArray(),
                ],
            ],
            'xaxis' => [
                'categories' => $spendingData->keys()->toArray(),
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                    'title' => [
                        'text' => 'Date',
                    ],
                ],
            ],
            'yaxis' => [
                'title' => [
                    'text' => 'Spending (₦)',
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
