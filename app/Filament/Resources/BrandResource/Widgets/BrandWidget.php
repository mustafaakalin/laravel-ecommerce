<?php

namespace App\Filament\Resources\BrandResource\Widgets;

use Filament\Widgets\ChartWidget;

class BrandWidget extends ChartWidget
{
    protected ?string $heading = 'Markalara Göre Ürün Sayısı';

    // Widget görünürlük kontrolü
    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    protected function getType(): string
    {
        return 'pie'; // Pie chart tipi
    }

    protected function getData(): array
    {
        // Markalara göre ürün sayısını al
        $brands = \App\Models\Brand::withCount('products')->get();

        // Veri setlerini oluştur
        $labels = $brands->pluck('name')->toArray(); // Marka isimleri
        $data = $brands->pluck('products_count')->toArray(); // Ürün sayıları

        return [
            'datasets' => [
                [
                    'label' => 'Ürün Sayısı',
                    'data' => $data,
                    'backgroundColor' => $this->generateColors(count($labels)),
                ],
            ],
            'labels' => $labels,
        ];
    }

    /**
     * Dinamik renkler oluşturmak için yardımcı metod.
     */
    private function generateColors(int $count): array
    {
        $colors = [];
        for ($i = 0; $i < $count; $i++) {
            $colors[] = sprintf('#%06X', mt_rand(0, 0xFFFFFF)); // Rastgele renkler
        }
        return $colors;
    }
}
