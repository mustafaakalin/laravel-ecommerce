<?php

namespace App\Filament\Resources\CampaignProductResource\Widgets;

use Filament\Widgets\ChartWidget;

class CampaignProductWidget extends ChartWidget
{
    protected ?string $heading = 'Kampanyalara Göre Ürün Sayısı';

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
        // Kampanyalara göre ürün sayısını al
        $campaignData = \App\Models\CampaignProduct::query()
            ->with('campaign') // Kampanya ilişkisini yükle
            ->selectRaw('campaign_id, COUNT(*) as product_count')
            ->groupBy('campaign_id')
            ->get()
            ->mapWithKeys(function ($record) {
                return [
                    $record->campaign->name ?? 'Bilinmeyen Kampanya' => $record->product_count,
                ];
            });

        return [
            'datasets' => [
                [
                    'label' => 'Ürün Sayısı',
                    'data' => $campaignData->values()->all(),
                    'backgroundColor' => '#42a5f5',
                ],
            ],
            'labels' => $campaignData->keys()->all(),
        ];
    }
}
