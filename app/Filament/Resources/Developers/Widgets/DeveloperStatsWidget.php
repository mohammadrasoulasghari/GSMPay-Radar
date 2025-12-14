<?php

namespace App\Filament\Resources\Developers\Widgets;

use App\Models\Developer;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;

class DeveloperStatsWidget extends BaseWidget
{
    public ?Model $record = null;

    protected function getStats(): array
    {
        /** @var Developer $developer */
        $developer = $this->record;

        if (!$developer) {
            return [];
        }

        $totalReports = $developer->getTotalReportsCount();
        $avgToneScore = $developer->getAverageToneScore();
        $avgComplianceRate = $developer->getAverageComplianceRate();
        $healthStatus = $developer->getOverallHealthStatus();

        return [
            Stat::make(
                __('developer.stats.total_reports'),
                $totalReports > 0 ? $totalReports : '-'
            )
                ->description(__('developer.stats.total_reports_desc'))
                ->icon('heroicon-o-document-text')
                ->color('primary'),

            Stat::make(
                __('developer.stats.avg_tone_score'),
                $avgToneScore !== null ? number_format($avgToneScore, 1) : '-'
            )
                ->description(__('developer.stats.avg_tone_score_desc'))
                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                ->color(Developer::scoreToColor($avgToneScore)),

            Stat::make(
                __('developer.stats.compliance_rate'),
                $avgComplianceRate !== null ? number_format($avgComplianceRate, 1) . '%' : '-'
            )
                ->description(__('developer.stats.compliance_rate_desc'))
                ->icon('heroicon-o-check-badge')
                ->color(Developer::scoreToColor($avgComplianceRate)),

            Stat::make(
                __('developer.stats.health_status'),
                __("developer.health.{$healthStatus}")
            )
                ->description(__('developer.stats.health_status_desc'))
                ->icon($this->getHealthIcon($healthStatus))
                ->color(Developer::getHealthBadgeColor($healthStatus)),
        ];
    }

    protected function getHealthIcon(string $status): string
    {
        return match ($status) {
            'healthy' => 'heroicon-o-heart',
            'warning' => 'heroicon-o-exclamation-triangle',
            'critical' => 'heroicon-o-x-circle',
            default => 'heroicon-o-question-mark-circle',
        };
    }

    protected function getColumns(): int
    {
        return 4;
    }
}
