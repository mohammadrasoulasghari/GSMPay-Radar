<?php

namespace App\Filament\Resources\PrReports\Pages;

use App\Filament\Resources\PrReports\PrReportResource;
use App\Filament\Resources\PrReports\Widgets\PrReportStatsWidget;
use App\Filament\Resources\PrReports\Widgets\QualityMetricsWidget;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewPrReport extends ViewRecord
{
    protected static string $resource = PrReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_github')
                ->label(__('pr_report.view_on_github'))
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(fn () => $this->record->pr_link)
                ->openUrlInNewTab()
                ->visible(fn () => !empty($this->record->pr_link))
                ->color('gray'),
            EditAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PrReportStatsWidget::class,
            QualityMetricsWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 4;
    }

    public function getTitle(): string
    {
        return $this->record->title ?? 'گزارش PR #' . $this->record->pr_number;
    }

    public function getSubheading(): ?string
    {
        return $this->record->repository . ' #' . $this->record->pr_number;
    }
}
