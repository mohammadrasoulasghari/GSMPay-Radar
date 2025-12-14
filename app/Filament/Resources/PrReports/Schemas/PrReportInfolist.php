<?php

namespace App\Filament\Resources\PrReports\Schemas;

use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class PrReportInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Section 1: Executive Header
                Section::make(__('pr_report.executive_header'))
                    ->schema([
                        // PR Title with link action
                        Split::make([
                            TextEntry::make('title')
                                ->label(__('pr_report.pr_details'))
                                ->placeholder('Untitled PR')
                                ->weight(FontWeight::Bold)
                                ->size(TextEntry\TextEntrySize::Large),
                            TextEntry::make('pr_link')
                                ->label(false)
                                ->formatStateUsing(fn () => '')
                                ->suffixAction(
                                    Action::make('view_github')
                                        ->label(__('pr_report.view_on_github'))
                                        ->icon('heroicon-o-arrow-top-right-on-square')
                                        ->url(fn ($record) => $record->pr_link)
                                        ->openUrlInNewTab()
                                        ->visible(fn ($record) => !empty($record->pr_link))
                                ),
                        ]),

                        // 4-Card Status Grid
                        Grid::make(4)
                            ->schema([
                                // Health Status Card
                                TextEntry::make('health_status')
                                    ->label(__('pr_report.health_status'))
                                    ->badge()
                                    ->icon('heroicon-o-heart')
                                    ->color(fn ($record) => $record->getHealthColor())
                                    ->formatStateUsing(fn ($state) => __('pr_report.' . ($state ?? 'unknown'))),

                                // Risk Level Card
                                TextEntry::make('risk_level')
                                    ->label(__('pr_report.risk_level'))
                                    ->badge()
                                    ->icon('heroicon-o-shield-check')
                                    ->color(fn ($record) => $record->getRiskColor())
                                    ->formatStateUsing(fn ($state) => __('pr_report.' . ($state ?? 'unknown'))),

                                // Business Value Score Card
                                TextEntry::make('business_value_score')
                                    ->label(__('pr_report.business_value'))
                                    ->badge()
                                    ->icon('heroicon-o-chart-bar')
                                    ->color(fn ($record) => $record->getBusinessValueColor())
                                    ->formatStateUsing(fn ($state) => ($state ?? 0) . '/100'),

                                // Change Type Card
                                TextEntry::make('change_type')
                                    ->label(__('pr_report.change_type'))
                                    ->badge()
                                    ->icon('heroicon-o-tag')
                                    ->color('info')
                                    ->formatStateUsing(fn ($state) => __('pr_report.' . ($state ?? 'unknown'))),
                            ]),
                    ])
                    ->collapsible(),

                // Section 2: Author Deep Dive (2-Column Grid)
                Section::make(__('pr_report.author_analysis'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                // Left Column: Quality Metrics
                                Group::make([
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
                                ])->columnSpan(1),

                                // Right Column: Learning Path
                                Group::make([
                                    // Recurring Mistakes
                                    TextEntry::make('recurring_mistakes')
                                        ->label(__('pr_report.recurring_mistakes'))
                                        ->listWithLineBreaks()
                                        ->bulleted()
                                        ->formatStateUsing(function ($record) {
                                            $mistakes = $record->getRecurringMistakes();
                                            return !empty($mistakes) ? $mistakes : [__('pr_report.no_mistakes')];
                                        })
                                        ->color(fn ($record) => empty($record->getRecurringMistakes()) ? 'success' : 'warning'),

                                    // Educational Path
                                    RepeatableEntry::make('educational_path')
                                        ->label(__('pr_report.educational_recommendations'))
                                        ->schema([
                                            TextEntry::make('title')
                                                ->label(false)
                                                ->icon('heroicon-o-book-open')
                                                ->weight(FontWeight::Bold),
                                            TextEntry::make('reason_fa')
                                                ->label(false)
                                                ->color('gray'),
                                        ])
                                        ->formatStateUsing(function ($record) {
                                            return $record->getEducationalPath();
                                        })
                                        ->hidden(fn ($record) => empty($record->getEducationalPath()))
                                        ->contained(false),

                                    TextEntry::make('no_education')
                                        ->label(false)
                                        ->formatStateUsing(fn () => __('pr_report.no_recommendations'))
                                        ->color('success')
                                        ->hidden(fn ($record) => !empty($record->getEducationalPath())),
                                ])->columnSpan(1),
                            ]),
                    ])
                    ->collapsible(),

                // Section 3: Gamification & Reviewers (Tabs)
                Section::make(__('pr_report.achievements_feedback'))
                    ->schema([
                        Tabs::make('sections')
                            ->tabs([
                                // Badges Tab
                                Tabs\Tab::make(__('pr_report.badges_earned'))
                                    ->schema([
                                        RepeatableEntry::make('badges')
                                            ->label(false)
                                            ->schema([
                                                Grid::make(3)
                                                    ->schema([
                                                        TextEntry::make('name')
                                                            ->label(false)
                                                            ->weight(FontWeight::Bold)
                                                            ->size(TextEntry\TextEntrySize::Large),
                                                        TextEntry::make('icon')
                                                            ->label(false),
                                                        TextEntry::make('reason_fa')
                                                            ->label(false)
                                                            ->color('gray'),
                                                    ]),
                                            ])
                                            ->formatStateUsing(function ($record) {
                                                return $record->getBadges();
                                            })
                                            ->hidden(fn ($record) => empty($record->getBadges()))
                                            ->contained(false),

                                        TextEntry::make('no_badges')
                                            ->label(false)
                                            ->formatStateUsing(fn () => __('pr_report.no_badges'))
                                            ->color('gray')
                                            ->italic()
                                            ->hidden(fn ($record) => !empty($record->getBadges())),
                                    ]),

                                // Reviewers Tab
                                Tabs\Tab::make(__('pr_report.reviewers'))
                                    ->schema([
                                        RepeatableEntry::make('reviewers')
                                            ->label(false)
                                            ->schema([
                                                Grid::make(3)
                                                    ->schema([
                                                        TextEntry::make('reviewer')
                                                            ->label('Reviewer')
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
                                                    ]),
                                            ])
                                            ->formatStateUsing(function ($record) {
                                                $reviewers = $record->getReviewersAnalytics();
                                                return array_slice($reviewers, 0, 5); // Limit to top 5
                                            })
                                            ->hidden(fn ($record) => empty($record->getReviewersAnalytics()))
                                            ->contained(false),

                                        TextEntry::make('no_reviewers')
                                            ->label(false)
                                            ->formatStateUsing(fn () => __('pr_report.no_reviewer_feedback'))
                                            ->color('gray')
                                            ->italic()
                                            ->hidden(fn ($record) => !empty($record->getReviewersAnalytics())),
                                    ]),
                            ]),
                    ])
                    ->collapsible(),

                // Section 4: Technical Debt
                Section::make(__('pr_report.technical_debt'))
                    ->schema([
                        // Over-Engineering Alert
                        TextEntry::make('over_engineering_alert')
                            ->label(__('pr_report.over_engineering'))
                            ->formatStateUsing(fn () => '⚠️ ' . __('pr_report.over_engineering_message'))
                            ->color('warning')
                            ->weight(FontWeight::Bold)
                            ->hidden(fn ($record) => !$record->isOverEngineered()),

                        // Refactoring Suggestions
                        TextEntry::make('refactoring_suggestions')
                            ->label(__('pr_report.refactoring_opportunities'))
                            ->listWithLineBreaks()
                            ->bulleted()
                            ->icon('heroicon-o-wrench')
                            ->formatStateUsing(function ($record) {
                                $suggestions = $record->getRefactoringSuggestions();
                                return !empty($suggestions) ? $suggestions : [__('pr_report.no_refactoring_needed')];
                            })
                            ->color(fn ($record) => empty($record->getRefactoringSuggestions()) ? 'success' : 'gray'),
                    ])
                    ->collapsible(),
            ]);
    }
}
