<?php

namespace App\Filament\Resources\PrReports\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class PrReportInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // Executive Summary Section
                Section::make(__('pr_report.executive_summary'))
                    ->icon('heroicon-o-clipboard-document-list')
                    ->schema([
                        TextEntry::make('title_summary')
                            ->label(__('pr_report.title_summary'))
                            ->state(fn ($record) => $record->getTitleSummary())
                            ->weight(FontWeight::SemiBold)
                            ->columnSpanFull(),

                        TextEntry::make('final_verdict')
                            ->label(__('pr_report.final_verdict'))
                            ->state(fn ($record) => $record->getFinalVerdict() ?: '-')
                            ->color(fn ($record) => match($record->health_status) {
                                'healthy' => 'success',
                                'warning' => 'warning',
                                'critical' => 'danger',
                                default => 'gray',
                            })
                            ->columnSpanFull(),

                        TextEntry::make('developer.name')
                            ->label(__('pr_report.developer'))
                            ->icon('heroicon-m-user')
                            ->iconColor('primary')
                            ->default(fn ($record) => $record->developer?->name ?? $record->developer?->username ?? '-')
                            ->url(fn ($record) => "/admin/developers/{$record->developer_id}"),

                        TextEntry::make('repository')
                            ->label(__('pr_report.repository'))
                            ->icon('heroicon-m-folder')
                            ->iconColor('gray'),

                        TextEntry::make('pr_number')
                            ->label(__('pr_report.pr_number'))
                            ->icon('heroicon-m-hashtag')
                            ->iconColor('gray')
                            ->prefix('#'),

                        TextEntry::make('created_at')
                            ->label(__('pr_report.created_at'))
                            ->dateTime('Y/m/d H:i')
                            ->icon('heroicon-m-calendar')
                            ->iconColor('gray'),
                    ])
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                        'lg' => 4,
                    ])
                    ->compact(),

                // Alerts Section - Only shows if there are issues
                Section::make(__('pr_report.alerts'))
                    ->icon('heroicon-o-exclamation-triangle')
                    ->schema([
                        TextEntry::make('over_engineering')
                            ->label(__('pr_report.over_engineering'))
                            ->state(fn () => '⚠️ ' . __('pr_report.over_engineering_message'))
                            ->color('warning')
                            ->weight(FontWeight::Bold)
                            ->hidden(fn ($record) => !$record->isOverEngineered()),

                        TextEntry::make('hr_flag')
                            ->label(__('pr_report.hr_attention'))
                            ->state(fn () => '🚨 ' . __('pr_report.hr_attention_message'))
                            ->color('danger')
                            ->weight(FontWeight::Bold)
                            ->hidden(fn ($record) => !$record->requiresHrAttention()),

                        TextEntry::make('blocking')
                            ->label(__('pr_report.blocking_pr'))
                            ->state(fn () => '🔴 ' . __('pr_report.blocking_pr_message'))
                            ->color('danger')
                            ->weight(FontWeight::Bold)
                            ->hidden(fn ($record) => !$record->isBlocking()),
                    ])
                    ->columns(3)
                    ->compact()
                    ->hidden(fn ($record) => !$record->isOverEngineered() && !$record->requiresHrAttention() && !$record->isBlocking())
                    ->extraAttributes(['class' => 'bg-warning-50 dark:bg-warning-950/20']),

                // Recurring Mistakes Section
                Section::make(__('pr_report.recurring_mistakes'))
                    ->icon('heroicon-o-exclamation-circle')
                    ->schema([
                        TextEntry::make('mistakes')
                            ->label(false)
                            ->state(fn ($record) => $record->getRecurringMistakes() ?: [__('pr_report.no_mistakes')])
                            ->listWithLineBreaks()
                            ->bulleted()
                            ->color(fn ($record) => empty($record->getRecurringMistakes()) ? 'success' : 'warning'),
                    ])
                    ->compact()
                    ->collapsed(fn ($record) => empty($record->getRecurringMistakes())),

                // Educational Path Section
                Section::make(__('pr_report.educational_recommendations'))
                    ->icon('heroicon-o-academic-cap')
                    ->schema([
                        RepeatableEntry::make('educational_items')
                            ->label(false)
                            ->schema([
                                TextEntry::make('title')
                                    ->label(__('pr_report.topic'))
                                    ->icon('heroicon-o-book-open')
                                    ->weight(FontWeight::Bold),
                                TextEntry::make('reason_fa')
                                    ->label(__('pr_report.reason'))
                                    ->color('gray'),
                            ])
                            ->state(fn ($record) => $record->getEducationalPath() ?: null)
                            ->columns(2)
                            ->contained(false)
                            ->hidden(fn ($record) => empty($record->getEducationalPath())),

                        TextEntry::make('no_education')
                            ->label(false)
                            ->state(fn () => '✓ ' . __('pr_report.no_recommendations'))
                            ->color('success')
                            ->hidden(fn ($record) => !empty($record->getEducationalPath())),
                    ])
                    ->compact()
                    ->collapsed(fn ($record) => empty($record->getEducationalPath())),

                // Gamification Badges Section
                Section::make(__('pr_report.badges_earned'))
                    ->icon('heroicon-o-trophy')
                    ->schema([
                        RepeatableEntry::make('badge_items')
                            ->label(false)
                            ->schema([
                                TextEntry::make('icon')
                                    ->label(false),
                                TextEntry::make('name')
                                    ->label(__('pr_report.badge_name'))
                                    ->weight(FontWeight::Bold),
                                TextEntry::make('reason_fa')
                                    ->label(__('pr_report.badge_reason'))
                                    ->color('gray'),
                            ])
                            ->state(fn ($record) => $record->getBadges() ?: null)
                            ->columns(3)
                            ->contained(false)
                            ->hidden(fn ($record) => empty($record->getBadges())),

                        TextEntry::make('no_badges')
                            ->label(false)
                            ->state(fn () => __('pr_report.no_badges'))
                            ->color('gray')
                            ->hidden(fn ($record) => !empty($record->getBadges())),
                    ])
                    ->compact()
                    ->extraAttributes(['class' => 'bg-primary-50 dark:bg-primary-950/20']),

                // Reviewers Analytics Section
                Section::make(__('pr_report.reviewers'))
                    ->icon('heroicon-o-users')
                    ->schema([
                        RepeatableEntry::make('reviewer_items')
                            ->label(false)
                            ->schema([
                                TextEntry::make('reviewer')
                                    ->label(__('pr_report.reviewer_name'))
                                    ->icon('heroicon-m-user')
                                    ->weight(FontWeight::Bold),
                                TextEntry::make('tone_score')
                                    ->label(__('pr_report.tone_score'))
                                    ->badge()
                                    ->formatStateUsing(fn ($state) => number_format($state ?? 0, 1) . '/10')
                                    ->color(fn ($state) => \App\Models\PrReport::getToneScoreColor($state ?? 0)),
                                TextEntry::make('nitpicking_ratio')
                                    ->label(__('pr_report.nitpicking_ratio'))
                                    ->badge()
                                    ->formatStateUsing(fn ($state) => round(($state ?? 0) * 100) . '%')
                                    ->color(fn ($state) => ($state ?? 0) > 0.3 ? 'warning' : 'gray'),
                            ])
                            ->state(fn ($record) => array_slice($record->getReviewersAnalytics(), 0, 5) ?: null)
                            ->columns(3)
                            ->contained(false)
                            ->hidden(fn ($record) => empty($record->getReviewersAnalytics())),

                        TextEntry::make('no_reviewers')
                            ->label(false)
                            ->state(fn () => __('pr_report.no_reviewer_feedback'))
                            ->color('gray')
                            ->hidden(fn ($record) => !empty($record->getReviewersAnalytics())),
                    ])
                    ->compact()
                    ->collapsible(),

                // Refactoring Suggestions Section
                Section::make(__('pr_report.refactoring_opportunities'))
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->schema([
                        TextEntry::make('suggestions')
                            ->label(false)
                            ->state(fn ($record) => $record->getRefactoringSuggestions() ?: [__('pr_report.no_refactoring_needed')])
                            ->listWithLineBreaks()
                            ->bulleted()
                            ->icon('heroicon-o-wrench')
                            ->color(fn ($record) => empty($record->getRefactoringSuggestions()) ? 'success' : 'gray'),
                    ])
                    ->compact()
                    ->collapsed(fn ($record) => empty($record->getRefactoringSuggestions())),
            ]);
    }
}
