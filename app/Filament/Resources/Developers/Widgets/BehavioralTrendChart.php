<?php

namespace App\Filament\Resources\Developers\Widgets;

use App\Models\Developer;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;

class BehavioralTrendChart extends ChartWidget
{
    public ?Model $record = null;

    protected ?string $heading = null;

    protected ?string $maxHeight = '300px';

    protected int | string | array $columnSpan = 1;

    public function getHeading(): ?string
    {
        return __('developer.charts.behavioral_trend');
    }

    public function getDescription(): ?string
    {
        /** @var Developer $developer */
        $developer = $this->record;

        if ($developer) {
            $trend = $developer->getToneScoreTrend();
            if ($trend === 'declining') {
                return __('developer.trend.burnout_warning');
            }
        }

        return __('developer.charts.behavioral_trend_desc');
    }

    protected function getData(): array
    {
        /** @var Developer $developer */
        $developer = $this->record;

        if (!$developer) {
            return $this->getEmptyData();
        }

        $trendData = $developer->getTrendData();

        // Filter out entries with no tone score
        $trendData = array_filter($trendData, fn ($item) => $item['tone_score'] > 0);
        $trendData = array_values($trendData); // Re-index

        if (count($trendData) < 2) {
            return $this->getEmptyData();
        }

        $labels = array_column($trendData, 'date');
        $toneScores = array_column($trendData, 'tone_score');

        // Determine line color based on trend
        $trend = $developer->getToneScoreTrend();
        $lineColor = match ($trend) {
            'declining' => 'rgb(239, 68, 68)', // Red
            'improving' => 'rgb(34, 197, 94)', // Green
            default => 'rgb(99, 102, 241)', // Indigo (stable)
        };

        return [
            'datasets' => [
                [
                    'label' => __('developer.charts.tone_score'),
                    'data' => $toneScores,
                    'borderColor' => $lineColor,
                    'backgroundColor' => str_replace('rgb', 'rgba', str_replace(')', ', 0.1)', $lineColor)),
                    'fill' => true,
                    'tension' => 0.3,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
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
                    'label' => __('developer.charts.tone_score'),
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
        $trendData = $developer ? $developer->getTrendData() : [];
        $filteredData = array_filter($trendData, fn ($item) => $item['tone_score'] > 0);
        $hasData = count($filteredData) >= 2;

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
