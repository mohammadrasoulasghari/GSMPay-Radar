<?php

namespace App\Filament\Resources\PrReports\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PrReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('developer_id')
                    ->relationship('developer', 'name')
                    ->required(),
                TextInput::make('repository')
                    ->required(),
                TextInput::make('pr_number')
                    ->required(),
                TextInput::make('pr_link'),
                TextInput::make('title'),
                TextInput::make('business_value_score')
                    ->numeric()
                    ->default(0),
                TextInput::make('solid_compliance_score')
                    ->numeric()
                    ->default(0),
                TextInput::make('tone_score')
                    ->numeric()
                    ->default(0.0),
                TextInput::make('health_status'),
                TextInput::make('risk_level'),
                TextInput::make('change_type'),
                TextInput::make('raw_analysis')
                    ->required(),
            ]);
    }
}
