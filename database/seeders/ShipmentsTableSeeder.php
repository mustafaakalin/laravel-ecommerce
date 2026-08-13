<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use App\Models\Order;

class ShipmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $carriers = [
            'Aras Kargo', 'Yurtiçi Kargo', 'PTT Kargo', 'MNG Kargo', 'UPS',
            'DHL', 'FedEx', 'TNT', 'Sürat Kargo', 'Hızlı Kargo', 'Kargo Takip',
            'Ulusoy Kargo', 'Birleşik Kargo', 'Ege Kargo', 'İstanbul Kargo'
        ];

        $orders = Order::all();

        foreach ($orders as $order) {
            for ($i = 0; $i < 3; $i++) { // Her sipariş için 3 kargo oluştur
                DB::table('shipments')->insert([
                    'order_id' => $order->id,
                    'tracking_number' => Str::random(10),
                    'carrier' => $faker->randomElement($carriers),
                    'status' => $faker->randomElement(['bekliyor', 'yolda', 'teslimedildi']),
                    'shipped_at' => $faker->dateTimeBetween('-1 month', 'now'),
                    'delivered_at' => $faker->dateTimeBetween('now', '+1 month'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
