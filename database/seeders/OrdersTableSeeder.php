<?php

namespace Database\Seeders;

use App\Models\Address;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Faker\Factory as Faker;

class OrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $users = User::all();
        $address = Address::all();

        for ($i = 0; $i < 10; $i++) {
            DB::table('orders')->insert([
                'user_id' => $users->random()->id,
                'address_id' => $address->random()->id,
                'total_price' => $faker->randomFloat(2, 10, 1000),
                // 'status' => $faker->randomElement(['bekliyor', 'yolda', 'tamamlandı']),
                'status' => $faker->randomElement(['pending', 'paid', 'cancelled', 'shipping', 'delivered']),
                'payment_method' => $faker->randomElement(['iyzico', 'stripe', 'paypal']),
                'payment_id' => $faker->uuid,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
