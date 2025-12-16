<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\PrReport;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class SkillGapChartWidget extends ChartWidget
{
    protected ?string $pollingInterval = '30s';

    protected int|string|array $columnSpan = 1;

    protected ?string $maxHeight = '300px';

    public function getHeading(): ?string
    {
        return __('dashboard.skill_gap');
    }

    public function getDescription(): ?string
    {
        return __('dashboard.skill_gap_desc');
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $aggregation = $this->getMistakesAggregation();

        // Define colors for each category
        $categoryColors = [
            'Testing' => 'rgba(59, 130, 246, 0.8)',      // Blue
            'Naming' => 'rgba(16, 185, 129, 0.8)',       // Green
            'Documentation' => 'rgba(139, 92, 246, 0.8)', // Purple
            'Architecture' => 'rgba(245, 158, 11, 0.8)',  // Amber
            'Performance' => 'rgba(239, 68, 68, 0.8)',    // Red
            'Security' => 'rgba(236, 72, 153, 0.8)',      // Pink
            'Other' => 'rgba(107, 114, 128, 0.8)',        // Gray
        ];

        $labels = array_keys($aggregation);
        $data = array_values($aggregation);
        $colors = array_map(fn($label) => $categoryColors[$label] ?? 'rgba(107, 114, 128, 0.8)', $labels);

        // Translate labels to Persian
        $persianLabels = array_map(fn($label) => __("dashboard.category_{$label}") ?? $label, $labels);

        return [
            'datasets' => [
                [
                    'label' => __('dashboard.mistake_count'),
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderColor' => $colors,
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $persianLabels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }

    /**
     * Get aggregated mistakes by category, cached for performance.
     */
    private function getMistakesAggregation(): array
    {
        return Cache::remember('dashboard:skill_gap', now()->addHours(2), function () {
            $reports = PrReport::where('created_at', '>=', now()->subMonth())
                ->whereNotNull('raw_analysis')
                ->get();

            $categoryCounts = array_fill_keys(
                ['Testing', 'Naming', 'Documentation', 'Architecture', 'Performance', 'Security', 'Other'],
                0
            );

            foreach ($reports as $pr) {
                $mistakes = $this->parseRecurringMistakes($pr->raw_analysis ?? []);
                foreach ($mistakes as $mistake) {
                    $category = $this->categorizeError($mistake);
                    $categoryCounts[$category]++;
                }
            }

            // Filter out zero counts and sort descending
            return collect($categoryCounts)
                ->filter(fn($count) => $count > 0)
                ->sortDesc()
                ->toArray();
        });
    }

    /**
     * Parse recurring mistakes from raw analysis JSON.
     */
    private function parseRecurringMistakes(array $rawAnalysis): array
    {
        $mistakes = $rawAnalysis['recurring_mistakes'] ?? [];

        if (!is_array($mistakes)) {
            return [];
        }

        // Flatten if nested arrays, and convert to strings
        return collect($mistakes)
            ->map(function ($mistake) {
                if (is_array($mistake)) {
                    return $mistake['description'] ?? $mistake['message'] ?? json_encode($mistake);
                }
                return (string) $mistake;
            })
            ->filter()
            ->toArray();
    }

    /**
     * Categorize an error message by keyword matching.
     */
    private function categorizeError(string $mistake): string
    {
        $mistake = strtolower($mistake);

        $categories = [
            'Testing' => ['test', 'unit', 'integration', 'coverage', 'mock', 'تست', 'پوشش'],
            'Naming' => ['name', 'clarity', 'variable', 'function', 'readable', 'نام', 'خوانایی'],
            'Documentation' => ['comment', 'doc', 'readme', 'explanation', 'describe', 'مستند', 'توضیح'],
            'Architecture' => ['design', 'pattern', 'structure', 'responsibility', 'separation', 'solid', 'معماری', 'ساختار'],
            'Performance' => ['slow', 'optimize', 'efficiency', 'memory', 'query', 'performance', 'کارایی', 'بهینه'],
            'Security' => ['vulnerability', 'injection', 'auth', 'validation', 'encrypt', 'security', 'امنیت'],
        ];

        foreach ($categories as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($mistake, $keyword)) {
                    return $category;
                }
            }
        }

        return 'Other';
    }
}
