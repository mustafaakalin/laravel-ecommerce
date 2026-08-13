<?php

namespace App\Filament\Resources\ShipmentDiscountResource\Pages;

use App\Filament\Resources\ShipmentDiscountResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditShipmentDiscount extends EditRecord
{
    protected static string $resource = ShipmentDiscountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            // Actions\DeleteAction::make(),
        ];
    }
}
