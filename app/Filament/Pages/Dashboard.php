<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\Dashboard\EngagementLeaderboardWidget;
use App\Filament\Widgets\Dashboard\RiskRadarWidget;
use App\Filament\Widgets\Dashboard\SkillGapChartWidget;
use App\Filament\Widgets\Dashboard\TeamPulseStatsWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static string $routePath = '/';

    protected static ?int $navigationSort = -2;

    public function getTitle(): string
    {
        return __('dashboard.page_title');
    }

    public static function getNavigationLabel(): string
    {
        return __('dashboard.page_title');
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-chart-bar';
    }

    public function getWidgets(): array
    {
        return [
            TeamPulseStatsWidget::class,
            RiskRadarWidget::class,
            SkillGapChartWidget::class,
            EngagementLeaderboardWidget::class,
        ];
    }
}
