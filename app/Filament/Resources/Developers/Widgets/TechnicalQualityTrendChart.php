<?php

namespace App\Filament\Resources\Developers\Widgets;

use App\Models\Developer;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;

class TechnicalQualityTrendChart extends ChartWidget
{
    public ?Model $record = null;

    protected ?string $heading = null;

    protected ?string $maxHeight = '300px';

    protected int | string | array $columnSpan = 1;

    public function getHeading(): ?string
    {
        return __('developer.charts.technical_quality');
    }

    public function getDescription(): ?string
    {
        return __('developer.charts.technical_quality_desc');
    }

    protected function getData(): array
    {
        /** @var Developer $developer */
        $developer = $this->record;

        if (!$developer) {
            return $this->getEmptyData();
        }

        $trendData = $developer->getTrendData();

        if (count($trendData) < 2) {
            return $this->getEmptyData();
        }

        $labels = array_column($trendData, 'date');
        $solidCompliance = array_column($trendData, 'solid_compliance');
        $businessValue = array_column($trendData, 'business_value');

        return [
            'datasets' => [
                [
                    'label' => __('developer.charts.solid_compliance'),
                    'data' => $solidCompliance,
                    'borderColor' => 'rgb(99, 102, 241)', // Indigo
                    'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => __('developer.charts.business_value'),
                    'data' => $businessValue,
                    'borderColor' => 'rgb(34, 197, 94)', // Green
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getEmptyData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => __('developer.charts.solid_compliance'),
                    'data' => [],
                    'borderColor' => 'rgb(99, 102, 241)',
                ],
            ],
            'labels' => [],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        /** @var Developer $developer */
        $developer = $this->record;
        $hasData = $developer && count($developer->getTrendData()) >= 2;

        return [
            'scales' => [
                'y' => [
                    'min' => 0,
                    'max' => 100,
                    'ticks' => [
                        'stepSize' => 20,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => $hasData,
                    'position' => 'bottom',
                ],
                'title' => [
                    'display' => !$hasData,
                    'text' => __('developer.charts.no_data_chart'),
                    'font' => [
                        'size' => 14,
                    ],
                ],
            ],
            'maintainAspectRatio' => true,
            'responsive' => true,
        ];
    }
}
