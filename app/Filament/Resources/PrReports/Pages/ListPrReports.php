<?php

namespace App\Filament\Resources\PrReports\Pages;

use App\Filament\Resources\PrReports\PrReportResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrReports extends ListRecords
{
    protected static string $resource = PrReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
