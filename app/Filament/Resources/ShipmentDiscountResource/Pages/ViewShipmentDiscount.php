<?php

namespace App\Filament\Resources\ShipmentDiscountResource\Pages;

use App\Filament\Resources\ShipmentDiscountResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewShipmentDiscount extends ViewRecord
{
    protected static string $resource = ShipmentDiscountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
