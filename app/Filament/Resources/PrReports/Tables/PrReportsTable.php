<?php

namespace App\Filament\Resources\PrReports\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PrReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('developer.name')
                    ->label(__('developer.name'))
                    ->searchable(),
                TextColumn::make('repository')
                    ->label(__('pr_report.repository'))
                    ->searchable(),
                TextColumn::make('pr_number')
                    ->label(__('pr_report.pr_number'))
                    ->searchable(),
                TextColumn::make('pr_link')
                    ->label(__('pr_report.pr_link'))
                    ->searchable(),
                TextColumn::make('title')
                    ->label(__('pr_report.title'))
                    ->searchable(),
                TextColumn::make('business_value_score')
                    ->label(__('pr_report.business_value'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('solid_compliance_score')
                    ->label(__('pr_report.solid_compliance'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tone_score')
                    ->label(__('pr_report.tone_score'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('health_status')
                    ->label(__('pr_report.health_status'))
                    ->searchable(),
                TextColumn::make('risk_level')
                    ->label(__('pr_report.risk_level'))
                    ->searchable(),
                TextColumn::make('change_type')
                    ->label(__('pr_report.change_type'))
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(__('pr_report.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('pr_report.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
