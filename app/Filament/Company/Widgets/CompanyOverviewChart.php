<?php

namespace App\Filament\Company\Widgets;

use App\Models\CompanyServiceRequest;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class CompanyOverviewChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'companyOverviewChart';
    protected static ?int $sort = 1;
    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'CompanyOverviewChart';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        $userId = auth()->id();

        // Bids Metrics
        $totalBids = CompanyServiceRequest::where('company_user_id', $userId)->count();
        $bidsWon = CompanyServiceRequest::where('company_user_id', $userId)->where('status', 'accepted')->count();
        $bidsLost = $totalBids - $bidsWon;
        return [
            'chart' => [
                'type' => 'pie',
                'height' => 300,
            ],
            'series' => [$bidsWon, $bidsLost],
            'labels' => ['Bids Won', 'Bids Lost'],
            'legend' => [
                'labels' => [
                    'fontFamily' => 'inherit',
                ],
            ],
        ];
    }
}
