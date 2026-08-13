<?php

namespace App\Filament\Resources\CampaignProductResource\Pages;

use App\Filament\Resources\CampaignProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCampaignProduct extends ViewRecord
{
    protected static string $resource = CampaignProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
