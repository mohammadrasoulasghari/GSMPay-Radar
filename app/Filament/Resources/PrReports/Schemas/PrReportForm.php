<?php

namespace App\Filament\Resources\PrReports\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PrReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('developer_id')
                    ->label(__('developer.name'))
                    ->relationship('developer', 'name')
                    ->required(),
                TextInput::make('repository')
                    ->label(__('pr_report.repository'))
                    ->required(),
                TextInput::make('pr_number')
                    ->label(__('pr_report.pr_number'))
                    ->required(),
                TextInput::make('pr_link')
                    ->label(__('pr_report.pr_link'))
                    ->url(),
                TextInput::make('title')
                    ->label(__('pr_report.title')),
                TextInput::make('business_value_score')
                    ->label(__('pr_report.business_value'))
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->default(0),
                TextInput::make('solid_compliance_score')
                    ->label(__('pr_report.solid_compliance'))
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->default(0),
                TextInput::make('tone_score')
                    ->label(__('pr_report.tone_score'))
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(10)
                    ->default(0.0),
                Select::make('health_status')
                    ->label(__('pr_report.health_status'))
                    ->options([
                        'healthy' => __('pr_report.healthy'),
                        'warning' => __('pr_report.warning'),
                        'critical' => __('pr_report.critical'),
                        'unknown' => __('pr_report.unknown'),
                    ]),
                Select::make('risk_level')
                    ->label(__('pr_report.risk_level'))
                    ->options([
                        'low' => __('pr_report.low'),
                        'medium' => __('pr_report.medium'),
                        'high' => __('pr_report.high'),
                    ]),
                Select::make('change_type')
                    ->label(__('pr_report.change_type'))
                    ->options([
                        'feature' => __('pr_report.feature'),
                        'bugfix' => __('pr_report.bugfix'),
                        'refactor' => __('pr_report.refactor'),
                        'docs' => __('pr_report.docs'),
                        'style' => __('pr_report.style'),
                        'test' => __('pr_report.test'),
                        'chore' => __('pr_report.chore'),
                    ]),
                Textarea::make('raw_analysis')
                    ->label(__('pr_report.raw_analysis'))
                    ->required()
                    ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $state)
                    ->dehydrateStateUsing(fn ($state) => json_decode($state, true))
                    ->rows(10),
            ]);
    }
}
