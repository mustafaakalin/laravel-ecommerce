<?php

namespace App\Filament\Resources\ShipmentDiscountResource\Pages;

use App\Filament\Resources\ShipmentDiscountResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShipmentDiscounts extends ListRecords
{
    protected static string $resource = ShipmentDiscountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
