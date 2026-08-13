<?php

namespace App\Filament\Resources\SliderForHomepageResource\Pages;

use App\Filament\Resources\SliderForHomepageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSliderForHomepages extends ListRecords
{
    protected static string $resource = SliderForHomepageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
