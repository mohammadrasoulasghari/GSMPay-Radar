<?php

namespace App\Filament\Resources\PrReports\Pages;

use App\Filament\Resources\PrReports\PrReportResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPrReport extends EditRecord
{
    protected static string $resource = PrReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
