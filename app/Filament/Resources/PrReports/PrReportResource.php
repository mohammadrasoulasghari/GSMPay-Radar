<?php

namespace App\Filament\Resources\PrReports;

use App\Filament\Resources\PrReports\Pages\CreatePrReport;
use App\Filament\Resources\PrReports\Pages\EditPrReport;
use App\Filament\Resources\PrReports\Pages\ListPrReports;
use App\Filament\Resources\PrReports\Pages\ViewPrReport;
use App\Filament\Resources\PrReports\Schemas\PrReportForm;
use App\Filament\Resources\PrReports\Schemas\PrReportInfolist;
use App\Filament\Resources\PrReports\Tables\PrReportsTable;
use App\Models\PrReport;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PrReportResource extends Resource
{
    protected static ?string $model = PrReport::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'گزارش‌های PR';
    
    protected static ?string $modelLabel = 'گزارش PR';
    
    protected static ?string $pluralModelLabel = 'گزارش‌های PR';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return PrReportForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PrReportInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PrReportsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPrReports::route('/'),
            'create' => CreatePrReport::route('/create'),
            'view' => ViewPrReport::route('/{record}'),
            'edit' => EditPrReport::route('/{record}/edit'),
        ];
    }
}
