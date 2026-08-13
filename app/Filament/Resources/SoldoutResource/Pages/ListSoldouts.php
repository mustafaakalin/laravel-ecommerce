<?php

namespace App\Filament\Resources\SoldoutResource\Pages;

use App\Filament\Resources\SoldoutResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSoldouts extends ListRecords
{
    protected static string $resource = SoldoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
