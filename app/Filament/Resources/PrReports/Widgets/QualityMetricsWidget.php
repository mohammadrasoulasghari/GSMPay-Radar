<?php

namespace App\Filament\Resources\PrReports\Widgets;

use App\Models\PrReport;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;

class QualityMetricsWidget extends BaseWidget
{
    public ?Model $record = null;

    protected function getStats(): array
    {
        /** @var PrReport $report */
        $report = $this->record;

        if (!$report) {
            return [];
        }

        $toneScore = $report->tone_score ?? 0;
        $testCoverage = $report->getTestCoverageQuality();
        $velocity = $report->getVelocityMetrics();

        return [
            Stat::make(
                __('pr_report.tone_score'),
                number_format($toneScore, 1) . '/10'
            )
                ->description(__('pr_report.tone_score_desc'))
                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                ->color(PrReport::getToneScoreColor($toneScore)),

            Stat::make(
                __('pr_report.test_coverage'),
                $testCoverage ?? 'N/A'
            )
                ->description(__('pr_report.test_coverage_desc'))
                ->icon('heroicon-o-beaker')
                ->color($this->getTestCoverageColor($testCoverage)),

            Stat::make(
                __('pr_report.change_type'),
                $this->getChangeTypeLabel($report->change_type)
            )
                ->description(__('pr_report.change_type_desc'))
                ->icon($this->getChangeTypeIcon($report->change_type))
                ->color('info'),

            Stat::make(
                __('pr_report.velocity'),
                $velocity['review_cycles'] ?? '-'
            )
                ->description(__('pr_report.review_cycles'))
                ->icon('heroicon-o-clock')
                ->color('gray'),
        ];
    }

    protected function getTestCoverageColor(?string $coverage): string
    {
        return match($coverage) {
            'high', 'excellent' => 'success',
            'medium', 'adequate' => 'warning',
            'low', 'poor' => 'danger',
            default => 'gray',
        };
    }

    protected function getChangeTypeLabel(?string $type): string
    {
        return match($type) {
            'feature' => 'ویژگی جدید',
            'bugfix' => 'رفع باگ',
            'refactor' => 'بازنویسی',
            'docs' => 'مستندات',
            'test' => 'تست',
            'chore' => 'نگهداری',
            'style' => 'استایل',
            'perf' => 'بهینه‌سازی',
            default => $type ?? 'نامشخص',
        };
    }

    protected function getChangeTypeIcon(?string $type): string
    {
        return match($type) {
            'feature' => 'heroicon-o-sparkles',
            'bugfix' => 'heroicon-o-bug-ant',
            'refactor' => 'heroicon-o-arrow-path',
            'docs' => 'heroicon-o-document-text',
            'test' => 'heroicon-o-beaker',
            'chore' => 'heroicon-o-wrench-screwdriver',
            'style' => 'heroicon-o-paint-brush',
            'perf' => 'heroicon-o-bolt',
            default => 'heroicon-o-tag',
        };
    }

    protected function getColumns(): int
    {
        return 4;
    }
}
