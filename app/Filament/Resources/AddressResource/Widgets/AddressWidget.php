<?php

namespace App\Filament\Resources\AddressResource\Widgets;

use Filament\Widgets\ChartWidget;

class AddressWidget extends ChartWidget
{
    protected ?string $heading = 'Adres İstatistikleri';

    protected static ?int $sort = 1;

    // Widget görünürlük kontrolü
    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    protected function getType(): string
    {
        return 'bar'; // Bar chart tipi
    }

    protected function getData(): array
    {
        // Kullanıcı başına adres sayısını al ve kullanıcı isimlerini ilişki üzerinden çek
        $addressesPerUser = \App\Models\Address::query()
            ->with('user') // Kullanıcı ilişkisini yükle
            ->selectRaw('COUNT(*) as count, user_id')
            ->groupBy('user_id')
            ->get()
            ->mapWithKeys(function ($address) {
                return [$address->user->name ?? 'Bilinmiyor' => $address->count];
            });

        return [
            'datasets' => [
                [
                    'label' => 'Kullanıcı Başına Adres Sayısı',
                    'data' => $addressesPerUser->values()->all(),
                    'backgroundColor' => '#4caf50',
                ],
            ],
            'labels' => $addressesPerUser->keys()->all(),
        ];
    }
}
