<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class CartItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Product ve Cart modellerinden verileri al
        $products = Product::all();
        $carts = Cart::all();

        // Her bir cart için rastgele product_id ve quantity değerleri oluştur
        foreach ($carts as $cart) {
            foreach ($products as $product) {
                // Quantity değerini product'ın stock değerine göre belirle
                $quantity = $faker->numberBetween(1, $product->stock);

                DB::table('cart_items')->insert([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
