<?php

namespace App\Filament\Resources\SliderForHomepageResource\Pages;

use App\Filament\Resources\SliderForHomepageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSliderForHomepage extends EditRecord
{
    protected static string $resource = SliderForHomepageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
