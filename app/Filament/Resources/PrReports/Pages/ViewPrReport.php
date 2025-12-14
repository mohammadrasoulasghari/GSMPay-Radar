<?php

namespace App\Filament\Resources\PrReports\Pages;

use App\Filament\Resources\PrReports\PrReportResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPrReport extends ViewRecord
{
    protected static string $resource = PrReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
