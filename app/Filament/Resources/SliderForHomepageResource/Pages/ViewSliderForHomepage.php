<?php

namespace App\Filament\Resources\SliderForHomepageResource\Pages;

use App\Filament\Resources\SliderForHomepageResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSliderForHomepage extends ViewRecord
{
    protected static string $resource = SliderForHomepageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
