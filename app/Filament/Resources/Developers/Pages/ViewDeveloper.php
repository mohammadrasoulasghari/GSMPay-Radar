<?php

namespace App\Filament\Resources\Developers\Pages;

use App\Filament\Resources\Developers\DeveloperResource;
use App\Filament\Resources\Developers\Widgets\DeveloperStatsWidget;
use App\Filament\Resources\Developers\Widgets\TechnicalQualityTrendChart;
use App\Filament\Resources\Developers\Widgets\BehavioralTrendChart;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDeveloper extends ViewRecord
{
    protected static string $resource = DeveloperResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            DeveloperStatsWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            TechnicalQualityTrendChart::class,
            BehavioralTrendChart::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 4;
    }

    public function getFooterWidgetsColumns(): int | array
    {
        return [
            'sm' => 1,
            'md' => 2,
            'lg' => 2,
        ];
    }

    public function getTitle(): string
    {
        return $this->record->name ?? $this->record->username;
    }

    public function getSubheading(): ?string
    {
        return '@' . $this->record->username;
    }
}
