<?php

namespace App\Filament\Resources\CampaignProductResource\Pages;

use App\Filament\Resources\CampaignProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCampaignProduct extends EditRecord
{
    protected static string $resource = CampaignProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
