<?php

namespace App\Filament\Resources\ProductAttributeValueResource\Pages;

use App\Filament\Resources\ProductAttributeValueResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProductAttributeValue extends ViewRecord
{
    protected static string $resource = ProductAttributeValueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
