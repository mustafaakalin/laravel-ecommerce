<?php

namespace Database\Seeders;

use App\Models\Order;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Product;
use Faker\Factory as Faker;

class SoldoutsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // En az 10 tane soldout kaydı oluştur
        for ($i = 0; $i < 10; $i++) {
            DB::table('soldouts')->insert([
                'user_id' => User::inRandomOrder()->first()->id,
                'product_id' => Product::inRandomOrder()->first()->id,
                'order_id' => Order::inRandomOrder()->first()->id,
                'is_sold' => $faker->boolean,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
