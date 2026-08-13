<?php

namespace App\Filament\Resources\SoldoutResource\Pages;

use App\Filament\Resources\SoldoutResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSoldout extends EditRecord
{
    protected static string $resource = SoldoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
