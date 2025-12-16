<?php

namespace App\Filament\Resources\Developers\RelationManagers;

use App\Filament\Resources\PrReports\PrReportResource;
use App\Models\Developer;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PrReportsRelationManager extends RelationManager
{
    protected static string $relationship = 'prReports';

    protected static ?string $title = null;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('developer.pr_reports');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('pr_report.title'))
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->title)
                    ->searchable()
                    ->url(fn ($record) => PrReportResource::getUrl('view', ['record' => $record]))
                    ->color('primary'),

                Tables\Columns\BadgeColumn::make('risk_level')
                    ->label(__('pr_report.risk_level'))
                    ->formatStateUsing(fn ($state) => __("developer.risk.{$state}") ?? $state)
                    ->color(fn ($state) => Developer::getRiskLevelColor($state ?? 'unknown')),

                Tables\Columns\TextColumn::make('health_status')
                    ->label(__('pr_report.health_status'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => __("developer.health.{$state}") ?? $state)
                    ->color(fn ($state) => Developer::getHealthBadgeColor($state ?? 'unknown'))
                    ->icon(fn ($state) => match ($state) {
                        'healthy' => 'heroicon-o-check-circle',
                        'warning' => 'heroicon-o-exclamation-triangle',
                        'critical' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-question-mark-circle',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('pr_report.created_at'))
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('risk_level')
                    ->label(__('developer.filters.risk_level'))
                    ->options([
                        'high' => __('developer.risk.high'),
                        'medium' => __('developer.risk.medium'),
                        'low' => __('developer.risk.low'),
                    ]),
            ])
            ->headerActions([
                // Read-only: no create action
            ])
            ->actions([
                ViewAction::make()
                    ->url(fn ($record) => PrReportResource::getUrl('view', ['record' => $record])),
            ])
            ->bulkActions([
                // Read-only: no bulk actions
            ])
            ->emptyStateHeading(__('developer.pr_reports_empty'))
            ->emptyStateDescription(__('developer.pr_reports_empty_desc'))
            ->emptyStateIcon('heroicon-o-document-text')
            ->paginated([10, 25, 50]);
    }
}
