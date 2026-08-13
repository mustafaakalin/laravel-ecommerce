<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Product;
use Faker\Factory as Faker;

class OrderItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // En az 50 tane ürün oluştur
        for ($i = 0; $i < 50; $i++) {
            // Rastgele bir Order seç
            $order = Order::inRandomOrder()->first();

            // Rastgele bir Product seç
            $product = Product::inRandomOrder()->first();

            // Quantity değeri, Product modelindeki stock değerinden fazla olamaz
            $quantity = $faker->numberBetween(1, $product->stock);

            // Price değeri, Product modelindeki price değerinden alınabilir
            $price = $product->price;

            // OrderItem tablosuna veri ekle
            DB::table('order_items')->insert([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $price,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
