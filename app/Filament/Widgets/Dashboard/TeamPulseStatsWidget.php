<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\PrReport;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class TeamPulseStatsWidget extends StatsOverviewWidget
{
    protected static ?string $pollingInterval = '30s';

    protected int|string|array $columnSpan = 'full';

    protected function getHeading(): ?string
    {
        return __('dashboard.team_pulse');
    }

    protected function getStats(): array
    {
        $metrics = $this->getCachedTeamMetrics();

        return [
            Stat::make(__('dashboard.prs_analyzed_week'), $metrics['prs_analyzed'])
                ->description(__('dashboard.prs_analyzed_week_desc'))
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('primary')
                ->chart($this->getWeeklyTrend()),

            Stat::make(__('dashboard.avg_code_health'), $metrics['avg_health'] . '%')
                ->description(__('dashboard.avg_code_health_desc'))
                ->descriptionIcon('heroicon-m-code-bracket')
                ->color($this->getHealthColor($metrics['avg_health']))
                ->chart($this->getHealthTrend()),

            Stat::make(__('dashboard.team_morale'), number_format($metrics['team_morale'], 1))
                ->description(__('dashboard.team_morale_desc'))
                ->descriptionIcon('heroicon-m-face-smile')
                ->color($this->getMoraleColor($metrics['team_morale']))
                ->chart($this->getMoraleTrend()),

            Stat::make(__('dashboard.critical_risks'), $metrics['critical_risks'])
                ->description(__('dashboard.critical_risks_desc'))
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($metrics['critical_risks'] > 0 ? 'danger' : 'success'),
        ];
    }

    /**
     * Get cached team metrics to reduce database queries.
     */
    private function getCachedTeamMetrics(): array
    {
        return Cache::remember('dashboard:team_pulse', now()->addHour(), function () {
            return [
                'prs_analyzed' => $this->getWeeklyPrCount(),
                'avg_health' => $this->getAvgCodeHealth(),
                'team_morale' => $this->getAvgTeamMorale(),
                'critical_risks' => $this->getCriticalRiskCount(),
            ];
        });
    }

    /**
     * Count PRs analyzed in the past week.
     */
    private function getWeeklyPrCount(): int
    {
        return PrReport::where('created_at', '>=', now()->subWeek())->count();
    }

    /**
     * Calculate average SOLID compliance score from past week.
     */
    private function getAvgCodeHealth(): float
    {
        $avg = PrReport::where('created_at', '>=', now()->subWeek())
            ->whereNotNull('solid_compliance_score')
            ->avg('solid_compliance_score');

        return round((float) ($avg ?? 0), 1);
    }

    /**
     * Calculate average tone score from past week.
     */
    private function getAvgTeamMorale(): float
    {
        $avg = PrReport::where('created_at', '>=', now()->subWeek())
            ->whereNotNull('tone_score')
            ->avg('tone_score');

        return round((float) ($avg ?? 0), 2);
    }

    /**
     * Count high-risk or critical PRs in the past week.
     */
    private function getCriticalRiskCount(): int
    {
        return PrReport::where('created_at', '>=', now()->subWeek())
            ->where(function ($query) {
                $query->where('risk_level', 'high')
                    ->orWhere('health_status', 'critical');
            })
            ->count();
    }

    /**
     * Get weekly PR count trend for sparkline chart.
     */
    private function getWeeklyTrend(): array
    {
        $trend = [];
        for ($i = 6; $i >= 0; $i--) {
            $trend[] = PrReport::whereDate('created_at', now()->subDays($i))->count();
        }
        return $trend;
    }

    /**
     * Get health score trend for sparkline chart.
     */
    private function getHealthTrend(): array
    {
        $trend = [];
        for ($i = 6; $i >= 0; $i--) {
            $avg = PrReport::whereDate('created_at', now()->subDays($i))
                ->whereNotNull('solid_compliance_score')
                ->avg('solid_compliance_score');
            $trend[] = (int) ($avg ?? 0);
        }
        return $trend;
    }

    /**
     * Get morale/tone trend for sparkline chart.
     */
    private function getMoraleTrend(): array
    {
        $trend = [];
        for ($i = 6; $i >= 0; $i--) {
            $avg = PrReport::whereDate('created_at', now()->subDays($i))
                ->whereNotNull('tone_score')
                ->avg('tone_score');
            $trend[] = round((float) ($avg ?? 0), 1);
        }
        return $trend;
    }

    /**
     * Determine color based on health score.
     */
    private function getHealthColor(float $health): string
    {
        if ($health >= 70) {
            return 'success';
        }
        if ($health >= 50) {
            return 'warning';
        }
        return 'danger';
    }

    /**
     * Determine color based on morale/tone score.
     */
    private function getMoraleColor(float $morale): string
    {
        if ($morale >= 7) {
            return 'success';
        }
        if ($morale >= 5) {
            return 'warning';
        }
        return 'danger';
    }
}
