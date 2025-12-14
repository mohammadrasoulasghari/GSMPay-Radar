<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\PrReport;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class RiskRadarWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    protected int|string|array $columnSpan = 2;

    protected static ?string $heading = null;

    public function getTableHeading(): ?string
    {
        return __('dashboard.risk_radar');
    }

    protected function getTableQuery(): Builder
    {
        return PrReport::query()
            ->with('developer')
            ->where(function ($query) {
                $query->where('risk_level', 'high')
                    ->orWhere('health_status', 'critical');
            })
            ->orderBy('created_at', 'desc')
            ->limit(10);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('developer.name')
                ->label(__('dashboard.developer_name'))
                ->searchable()
                ->url(fn (PrReport $record) => $record->developer 
                    ? route('filament.admin.resources.developers.view', $record->developer) 
                    : null)
                ->color('primary'),

            TextColumn::make('title')
                ->label(__('dashboard.pr_title'))
                ->limit(40)
                ->tooltip(fn (PrReport $record) => $record->title)
                ->url(fn (PrReport $record) => $record->pr_link)
                ->openUrlInNewTab()
                ->color('primary'),

            TextColumn::make('risk_reason')
                ->label(__('dashboard.risk_reason'))
                ->getStateUsing(fn (PrReport $record) => $this->getRiskReason($record))
                ->limit(50)
                ->tooltip(fn (PrReport $record) => $this->getRiskReason($record))
                ->color('danger')
                ->weight('bold'),

            TextColumn::make('created_at')
                ->label(__('dashboard.created_at'))
                ->since()
                ->dateTimeTooltip(),
        ];
    }

    protected function getTableRecordClasses(PrReport $record): ?string
    {
        if ($record->health_status === 'critical') {
            return 'bg-danger-50 dark:bg-danger-950';
        }
        if ($record->risk_level === 'high') {
            return 'bg-warning-50 dark:bg-warning-950';
        }
        return null;
    }

    /**
     * Extract a meaningful risk reason from the PR report.
     */
    private function getRiskReason(PrReport $record): string
    {
        $rawAnalysis = $record->raw_analysis ?? [];

        // Check for main_risk_factors
        if (!empty($rawAnalysis['main_risk_factors'])) {
            $factors = $rawAnalysis['main_risk_factors'];
            if (is_array($factors) && count($factors) > 0) {
                return is_string($factors[0]) ? $factors[0] : json_encode($factors[0]);
            }
        }

        // Fallback: first recurring mistake
        if (!empty($rawAnalysis['recurring_mistakes'])) {
            $mistakes = $rawAnalysis['recurring_mistakes'];
            if (is_array($mistakes) && count($mistakes) > 0) {
                return is_string($mistakes[0]) ? $mistakes[0] : json_encode($mistakes[0]);
            }
        }

        // Fallback: health_status description
        $statusMap = [
            'critical' => __('dashboard.status_critical'),
            'at_risk' => __('dashboard.status_at_risk'),
            'needs_attention' => __('dashboard.status_needs_attention'),
        ];

        return $statusMap[$record->health_status] ?? $record->health_status ?? __('dashboard.unknown_risk');
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return __('dashboard.no_risks_heading');
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return __('dashboard.no_risks_desc');
    }

    protected function getTableEmptyStateIcon(): ?string
    {
        return 'heroicon-o-shield-check';
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }
}
