<?php

namespace App\Filament\Resources\PrReports\Schemas;

use Filament\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class PrReportInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // PR Title with action
                TextEntry::make('title')
                    ->label(__('pr_report.pr_details'))
                    ->placeholder(__('pr_report.untitled_pr'))
                    ->weight(FontWeight::Bold)
                    ->suffixAction(
                        Action::make('view_github')
                            ->label(__('pr_report.view_on_github'))
                            ->icon('heroicon-o-arrow-top-right-on-square')
                            ->url(fn ($record) => $record->pr_link)
                            ->openUrlInNewTab()
                            ->visible(fn ($record) => !empty($record->pr_link))
                    )
                    ->columnSpanFull(),

                // 4-Card Status Row
                TextEntry::make('health_status')
                    ->label(__('pr_report.health_status'))
                    ->badge()
                    ->icon('heroicon-o-heart')
                    ->color(fn ($record) => $record->getHealthColor())
                    ->formatStateUsing(fn ($state) => __('pr_report.' . ($state ?? 'unknown'))),

                TextEntry::make('risk_level')
                    ->label(__('pr_report.risk_level'))
                    ->badge()
                    ->icon('heroicon-o-shield-check')
                    ->color(fn ($record) => $record->getRiskColor())
                    ->formatStateUsing(fn ($state) => __('pr_report.' . ($state ?? 'unknown'))),

                TextEntry::make('business_value_score')
                    ->label(__('pr_report.business_value'))
                    ->badge()
                    ->icon('heroicon-o-chart-bar')
                    ->color(fn ($record) => $record->getBusinessValueColor())
                    ->formatStateUsing(fn ($state) => ($state ?? 0) . '/100'),

                TextEntry::make('change_type')
                    ->label(__('pr_report.change_type'))
                    ->badge()
                    ->icon('heroicon-o-tag')
                    ->color('info')
                    ->formatStateUsing(fn ($state) => __('pr_report.' . ($state ?? 'unknown'))),

                // Quality Metrics
                TextEntry::make('solid_compliance_score')
                    ->label(__('pr_report.solid_compliance'))
                    ->badge()
                    ->color(fn ($record) => $record->getSolidColor())
                    ->formatStateUsing(fn ($state) => ($state ?? 0) . '/100'),

                TextEntry::make('velocity')
                    ->label(__('pr_report.velocity'))
                    ->formatStateUsing(fn ($record) => $record->getVelocity() ?? '-')
                    ->badge()
                    ->color('gray'),

                TextEntry::make('test_coverage')
                    ->label(__('pr_report.test_coverage'))
                    ->formatStateUsing(function ($record) {
                        $coverage = $record->getTestCoverage();
                        return $coverage !== null ? $coverage . '%' : '-';
                    })
                    ->badge()
                    ->color(function ($record) {
                        $coverage = $record->getTestCoverage();
                        if ($coverage === null) return 'gray';
                        return match(true) {
                            $coverage < 50 => 'danger',
                            $coverage < 80 => 'warning',
                            default => 'success',
                        };
                    }),

                TextEntry::make('tone_score')
                    ->label(__('pr_report.tone_score'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => number_format($state ?? 0, 1) . '/10')
                    ->color(fn ($state) => \App\Models\PrReport::getToneScoreColor($state ?? 0)),

                // Recurring Mistakes
                TextEntry::make('recurring_mistakes')
                    ->label(__('pr_report.recurring_mistakes'))
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->formatStateUsing(function ($record) {
                        $mistakes = $record->getRecurringMistakes();
                        return !empty($mistakes) ? $mistakes : [__('pr_report.no_mistakes')];
                    })
                    ->color(fn ($record) => empty($record->getRecurringMistakes()) ? 'success' : 'warning')
                    ->columnSpanFull(),

                // Educational Path
                RepeatableEntry::make('educational_path')
                    ->label(__('pr_report.educational_recommendations'))
                    ->schema([
                        TextEntry::make('title')
                            ->icon('heroicon-o-book-open')
                            ->weight(FontWeight::Bold),
                        TextEntry::make('reason_fa')
                            ->color('gray'),
                    ])
                    ->formatStateUsing(function ($record) {
                        $path = $record->getEducationalPath();
                        return !empty($path) ? $path : null;
                    })
                    ->hidden(fn ($record) => empty($record->getEducationalPath()))
                    ->contained(false)
                    ->columnSpanFull(),

                TextEntry::make('no_education')
                    ->label(false)
                    ->formatStateUsing(fn () => __('pr_report.no_recommendations'))
                    ->color('success')
                    ->hidden(fn ($record) => !empty($record->getEducationalPath()))
                    ->columnSpanFull(),

                // Gamification Badges
                RepeatableEntry::make('badges')
                    ->label(__('pr_report.badges_earned'))
                    ->schema([
                        TextEntry::make('name')
                            ->weight(FontWeight::Bold),
                        TextEntry::make('icon'),
                        TextEntry::make('reason_fa')
                            ->color('gray'),
                    ])
                    ->columns(3)
                    ->formatStateUsing(function ($record) {
                        $badges = $record->getBadges();
                        return !empty($badges) ? $badges : null;
                    })
                    ->hidden(fn ($record) => empty($record->getBadges()))
                    ->contained(false)
                    ->columnSpanFull(),

                TextEntry::make('no_badges')
                    ->label(false)
                    ->formatStateUsing(fn () => __('pr_report.no_badges'))
                    ->color('gray')
                    ->hidden(fn ($record) => !empty($record->getBadges()))
                    ->columnSpanFull(),

                // Reviewers Feedback
                RepeatableEntry::make('reviewers')
                    ->label(__('pr_report.reviewers'))
                    ->schema([
                        TextEntry::make('reviewer')
                            ->weight(FontWeight::Bold),
                        TextEntry::make('tone_score')
                            ->label(__('pr_report.tone_score'))
                            ->badge()
                            ->formatStateUsing(fn ($state) => number_format($state ?? 0, 1) . '/10')
                            ->color(fn ($state) => \App\Models\PrReport::getToneScoreColor($state ?? 0)),
                        TextEntry::make('nitpicking_ratio')
                            ->label(__('pr_report.nitpicking_ratio'))
                            ->formatStateUsing(fn ($state) => round(($state ?? 0) * 100) . '%')
                            ->badge()
                            ->color(fn ($state) => ($state ?? 0) > 0.3 ? 'warning' : 'gray'),
                    ])
                    ->columns(3)
                    ->formatStateUsing(function ($record) {
                        $reviewers = $record->getReviewersAnalytics();
                        return !empty($reviewers) ? array_slice($reviewers, 0, 5) : null;
                    })
                    ->hidden(fn ($record) => empty($record->getReviewersAnalytics()))
                    ->contained(false)
                    ->columnSpanFull(),

                TextEntry::make('no_reviewers')
                    ->label(false)
                    ->formatStateUsing(fn () => __('pr_report.no_reviewer_feedback'))
                    ->color('gray')
                    ->hidden(fn ($record) => !empty($record->getReviewersAnalytics()))
                    ->columnSpanFull(),

                // Technical Debt
                TextEntry::make('over_engineering_alert')
                    ->label(__('pr_report.over_engineering'))
                    ->formatStateUsing(fn () => '⚠️ ' . __('pr_report.over_engineering_message'))
                    ->color('warning')
                    ->weight(FontWeight::Bold)
                    ->hidden(fn ($record) => !$record->isOverEngineered())
                    ->columnSpanFull(),

                TextEntry::make('refactoring_suggestions')
                    ->label(__('pr_report.refactoring_opportunities'))
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->icon('heroicon-o-wrench')
                    ->formatStateUsing(function ($record) {
                        $suggestions = $record->getRefactoringSuggestions();
                        return !empty($suggestions) ? $suggestions : [__('pr_report.no_refactoring_needed')];
                    })
                    ->color(fn ($record) => empty($record->getRefactoringSuggestions()) ? 'success' : 'gray')
                    ->columnSpanFull(),
            ]);
    }
}
