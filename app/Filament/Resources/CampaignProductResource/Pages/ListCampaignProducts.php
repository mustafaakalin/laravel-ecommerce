<?php

namespace App\Filament\Resources\CampaignProductResource\Pages;

use App\Filament\Resources\CampaignProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCampaignProducts extends ListRecords
{
    protected static string $resource = CampaignProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
