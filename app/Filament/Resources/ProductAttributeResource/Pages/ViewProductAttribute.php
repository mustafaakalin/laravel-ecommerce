<?php

namespace App\Filament\Resources\ProductAttributeResource\Pages;

use App\Filament\Resources\ProductAttributeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProductAttribute extends ViewRecord
{
    protected static string $resource = ProductAttributeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
