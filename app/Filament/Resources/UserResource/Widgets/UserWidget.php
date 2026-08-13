<?php

namespace App\Filament\Resources\UserResource\Widgets;

use Filament\Widgets\ChartWidget;

class UserWidget extends ChartWidget
{
    protected ?string $heading = 'Chart';

    protected function getData(): array
    {
        return [
            //
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
