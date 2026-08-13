<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class ProductStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static bool $isLazy = false;
    protected ?string $pollingInterval = '10s';

    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    protected function getStats(): array
    {
        // Temel ürün istatistikleri
        $totalProducts = Product::count();
        $activeProducts = Product::active()->count();
        $featuredProducts = Product::featured()->count();
        
        // Stok durumu
        $outOfStock = Product::where('stock', 0)->count();
        $lowStock = Product::where('stock', '<=', 5)->where('stock', '>', 0)->count();

        // Sipariş İstatistikleri
        $totalOrders = Order::count();
        $pendingOrders = Order::pending()->count();
        $shippingOrders = Order::shipping()->count();
        $completedOrders = Order::completed()->count();

        // En çok satılan ürün (Tamamlanan siparişlerden)
        $bestSeller = DB::table('products')
            ->select('products.id', DB::raw('SUM(order_items.quantity) as total_quantity'))
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'completed')
            ->groupBy('products.id')
            ->orderByDesc('total_quantity')
            ->first();

        $bestSellerProduct = $bestSeller ? Product::find($bestSeller->id) : null;

        // Aktif sepetlerdeki en popüler ürün
        $mostInCart = Product::select('products.id', DB::raw('SUM(cart_items.quantity) as cart_quantity'))
            ->join('cart_items', 'products.id', '=', 'cart_items.product_id')
            ->groupBy('products.id')
            ->orderByDesc('cart_quantity')
            ->first();

        $mostInCartProduct = $mostInCart ? Product::find($mostInCart->id) : null;

        // Toplam satış tutarı (Tamamlanan siparişler)
        $totalSales = Order::where('status', 'completed')
            ->sum('total_price');

        // En çok görüntülenen ürün
        $mostViewed = Product::orderBy('view_count', 'desc')->first();
        
        // En çok yorum alan ürün
        $mostCommented = Product::withCount('comments')
            ->orderBy('comments_count', 'desc')
            ->first();

        // En çok Beğeni alan ürün
        $mostLiked = Product::withCount('likes')
            ->orderBy('likes_count', 'desc')
            ->first();
        
        return [
            // Temel İstatistikler
            Stat::make('Toplam Ürün', $totalProducts)
                ->description('Sistemdeki toplam ürün sayısı')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('success'),

            Stat::make('Aktif Ürünler', $activeProducts)
                ->description('Aktif durumda olan ürünler')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            // Stok Durumu
            Stat::make('Stok Durumu', "$outOfStock ürün tükendi")
                ->description("$lowStock ürün kritik stokta")
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color($outOfStock > 0 ? 'danger' : 'success'),

            // Sipariş İstatistikleri
            Stat::make('Toplam Sipariş', $totalOrders)
                ->description("$completedOrders delivered, $pendingOrders pending")
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('success'),

            Stat::make('Kargodaki Siparişler', $shippingOrders)
                ->description('Gönderim sürecinde')
                ->descriptionIcon('heroicon-m-truck')
                ->color('info'),

            // En Çok Satan Ürün
            Stat::make('En Çok Satan Ürün', $bestSellerProduct?->name ?? '-')
                ->description($bestSellerProduct ? "{$bestSeller->total_quantity} adet satıldı" : '')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),

            // Sepetteki En Popüler
            Stat::make('Sepette En Çok', $mostInCartProduct?->name ?? '-')
                ->description($mostInCartProduct ? "{$mostInCart->cart_quantity} adet sepette" : '')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('warning'),

            // Toplam Satış
            Stat::make('Toplam Satış', number_format($totalSales, 2) . ' ₺')
                ->description('Tamamlanan siparişlerden')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            // En Çok Görüntülenen
            Stat::make('En Çok Görüntülenen', $mostViewed?->name ?? '-')
                ->description($mostViewed ? "{$mostViewed->view_count} görüntülenme" : '')
                ->descriptionIcon('heroicon-m-eye')
                ->color('info'),

            // En Çok Yorum Alan
            Stat::make('En Çok Yorum Alan', $mostCommented?->name ?? '-')
                ->description($mostCommented ? "{$mostCommented->comments_count} yorum" : '')
                ->descriptionIcon('heroicon-m-chat-bubble-left')
                ->color('info'),

            // En Çok Yorum Alan
            Stat::make('En Çok Beğeni Alan', $mostLiked?->name ?? '-')
                ->description($mostLiked ? "{$mostLiked->likes_count} Beğeni" : '')
                ->descriptionIcon('heroicon-m-heart')
                ->color('info'),
        ];
    }
}