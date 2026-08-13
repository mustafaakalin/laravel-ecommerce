<?php

namespace App\Filament\Resources\CartItemResource\Widgets;

use Filament\Widgets\ChartWidget;

class CartItemWidget extends ChartWidget
{
    protected ?string $heading = 'Kategorilere Göre Sepet Ürünleri';

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
        // Her kategori için sepetteki toplam ürün adedini hesapla
        $categoryData = \App\Models\CartItem::query()
            ->with('product.category') // Product ve Category ilişkilerini yükle
            ->get()
            ->groupBy(fn($item) => $item->product->category->name ?? 'Bilinmeyen Kategori') // Kategorilere göre gruplandır
            ->map(fn($items) => $items->sum('quantity')); // Toplam ürün adedini hesapla

        return [
            'datasets' => [
                [
                    'label' => 'Ürün Adedi',
                    'data' => $categoryData->values()->all(),
                    'backgroundColor' => '#ff7043', // Grafik rengini ayarla
                ],
            ],
            'labels' => $categoryData->keys()->all(),
        ];
    }
}
