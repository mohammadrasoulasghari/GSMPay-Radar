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
                    ->label(__('developer.developer'))
                    ->relationship('developer', 'name')
                    ->required(),
                TextInput::make('repository')
                    ->label(__('pr_report.repository'))
                    ->required(),
                TextInput::make('pr_number')
                    ->label(__('pr_report.pr_number'))
                    ->required(),
                TextInput::make('pr_link')
                    ->label(__('pr_report.pr_link')),
                TextInput::make('title')
                    ->label(__('pr_report.title')),
                TextInput::make('business_value_score')
                    ->label(__('pr_report.business_value'))
                    ->numeric()
                    ->default(0),
                TextInput::make('solid_compliance_score')
                    ->label(__('pr_report.solid_compliance'))
                    ->numeric()
                    ->default(0),
                TextInput::make('tone_score')
                    ->label(__('pr_report.tone_score'))
                    ->numeric()
                    ->default(0.0),
                TextInput::make('health_status')
                    ->label(__('pr_report.health_status')),
                TextInput::make('risk_level')
                    ->label(__('pr_report.risk_level')),
                TextInput::make('change_type')
                    ->label(__('pr_report.change_type')),
                Textarea::make('raw_analysis')
                    ->label(__('pr_report.raw_analysis'))
                    ->required()
                    ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))
                    ->dehydrateStateUsing(fn ($state) => json_decode($state, true))
                    ->rows(10),
            ]);
    }
}
