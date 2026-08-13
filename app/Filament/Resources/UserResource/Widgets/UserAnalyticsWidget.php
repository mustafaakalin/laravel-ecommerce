<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\User;
use App\Models\Order;
use App\Models\Like;
use App\Models\Address;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class UserAnalyticsWidget extends ChartWidget
{
    protected ?string $heading = 'Bu Ay\'ın Kullanıcı Analizi';
    
    protected static ?int $sort = 2;
    
    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    protected function getData(): array
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Bu ayki kullanıcı sayısı
        $userCount = User::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        // Bu ayki sipariş sayısı
        $orderCount = Order::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        // Bu ayki adres sayısı
        $addressCount = Address::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        // Bu ayki beğeni sayısı
        $likeCount = Like::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Aktivite Sayıları',
                    'data' => [$userCount, $orderCount, $addressCount, $likeCount],
                    'backgroundColor' => ['#36A2EB', '#FF6384', '#4BC0C0', '#FFCE56'],
                ],
            ],
            'labels' => ['Yeni Kullanıcılar', 'Siparişler', 'Adresler', 'Beğeniler'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false, // Tek dataset olduğu için legend'ı kapattım
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
            'indexAxis' => 'y', // Yatay bar grafik için
        ];
    }
}