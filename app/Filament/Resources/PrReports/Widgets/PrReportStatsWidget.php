<?php

namespace App\Filament\Resources\PrReports\Widgets;

use App\Models\PrReport;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;

class PrReportStatsWidget extends BaseWidget
{
    public ?Model $record = null;

    protected function getStats(): array
    {
        /** @var PrReport $report */
        $report = $this->record;

        if (!$report) {
            return [];
        }

        return [
            Stat::make(
                __('pr_report.health_status'),
                $this->getHealthLabel($report->health_status)
            )
                ->description(__('pr_report.health_status_desc'))
                ->icon($this->getHealthIcon($report->health_status))
                ->color($report->getHealthColor()),

            Stat::make(
                __('pr_report.risk_level'),
                $this->getRiskLabel($report->risk_level)
            )
                ->description(__('pr_report.risk_level_desc'))
                ->icon($this->getRiskIcon($report->risk_level))
                ->color($report->getRiskColor()),

            Stat::make(
                __('pr_report.business_value'),
                ($report->business_value_score ?? 0) . '/100'
            )
                ->description(__('pr_report.business_value_desc'))
                ->icon('heroicon-o-chart-bar')
                ->color($report->getBusinessValueColor()),

            Stat::make(
                __('pr_report.solid_compliance'),
                ($report->solid_compliance_score ?? 0) . '/100'
            )
                ->description(__('pr_report.solid_compliance_desc'))
                ->icon('heroicon-o-cube')
                ->color($report->getSolidColor()),
        ];
    }

    protected function getHealthLabel(string $status): string
    {
        return match($status) {
            'healthy' => 'سالم ✓',
            'warning' => 'هشدار ⚠',
            'critical', 'danger' => 'بحرانی ✗',
            default => 'نامشخص',
        };
    }

    protected function getHealthIcon(string $status): string
    {
        return match($status) {
            'healthy' => 'heroicon-o-heart',
            'warning' => 'heroicon-o-exclamation-triangle',
            'critical', 'danger' => 'heroicon-o-x-circle',
            default => 'heroicon-o-question-mark-circle',
        };
    }

    protected function getRiskLabel(string $level): string
    {
        return match($level) {
            'low' => 'کم ↓',
            'medium' => 'متوسط →',
            'high' => 'بالا ↑',
            default => 'نامشخص',
        };
    }

    protected function getRiskIcon(string $level): string
    {
        return match($level) {
            'low' => 'heroicon-o-shield-check',
            'medium' => 'heroicon-o-shield-exclamation',
            'high' => 'heroicon-o-fire',
            default => 'heroicon-o-question-mark-circle',
        };
    }

    protected function getColumns(): int
    {
        return 4;
    }
}
