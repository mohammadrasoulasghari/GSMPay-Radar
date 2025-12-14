<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\PrReport;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class EngagementLeaderboardWidget extends Widget
{
    protected static string $view = 'filament.widgets.engagement-leaderboard-widget';

    protected static ?string $pollingInterval = '30s';

    protected int|string|array $columnSpan = 'full';

    /**
     * Get leaderboard data for the view.
     */
    public function getLeaderboardData(): array
    {
        return $this->extractReviewerMetrics();
    }

    /**
     * Get the widget heading for display.
     */
    public function getHeading(): string
    {
        return __('dashboard.leaderboard');
    }

    /**
     * Extract and aggregate reviewer metrics from PR reports.
     */
    private function extractReviewerMetrics(): array
    {
        return Cache::remember('dashboard:leaderboard', now()->addHour(), function () {
            $reports = PrReport::where('created_at', '>=', now()->subMonth())
                ->whereNotNull('raw_analysis')
                ->get();

            $reviewerScores = [];

            foreach ($reports as $pr) {
                $analytics = $pr->raw_analysis['reviewers_analytics'] ?? [];

                if (!is_array($analytics)) {
                    continue;
                }

                foreach ($analytics as $reviewer) {
                    if (!is_array($reviewer)) {
                        continue;
                    }

                    $name = $reviewer['reviewer_name'] ?? $reviewer['name'] ?? null;
                    if (!$name) {
                        continue;
                    }

                    if (!isset($reviewerScores[$name])) {
                        $reviewerScores[$name] = [
                            'name' => $name,
                            'scores' => [],
                        ];
                    }

                    $toneScore = $reviewer['tone_score'] ?? null;
                    if ($toneScore !== null) {
                        $reviewerScores[$name]['scores'][] = (float) $toneScore;
                    }
                }
            }

            // Calculate averages and sort
            return collect($reviewerScores)
                ->map(function ($data) {
                    $scores = $data['scores'];
                    $avgTone = count($scores) > 0
                        ? round(array_sum($scores) / count($scores), 2)
                        : 0;

                    return [
                        'name' => $data['name'],
                        'avg_tone' => $avgTone,
                        'review_count' => count($scores),
                    ];
                })
                ->filter(fn($item) => $item['review_count'] > 0)
                ->sortByDesc('avg_tone')
                ->take(10)
                ->values()
                ->toArray();
        });
    }

    /**
     * Get the medal emoji for a given rank.
     */
    public function getMedal(int $rank): string
    {
        return match ($rank) {
            1 => '🥇',
            2 => '🥈',
            3 => '🥉',
            default => (string) $rank,
        };
    }

    /**
     * Get the color class for a tone score.
     */
    public function getToneColor(float $score): string
    {
        if ($score >= 8) {
            return 'text-success-600 dark:text-success-400';
        }
        if ($score >= 6) {
            return 'text-warning-600 dark:text-warning-400';
        }
        return 'text-danger-600 dark:text-danger-400';
    }

    /**
     * Get background color class for a row based on rank.
     */
    public function getRowBackground(int $rank): string
    {
        return match ($rank) {
            1 => 'bg-yellow-50 dark:bg-yellow-900/20',
            2 => 'bg-gray-100 dark:bg-gray-800/50',
            3 => 'bg-amber-50 dark:bg-amber-900/20',
            default => '',
        };
    }
}
