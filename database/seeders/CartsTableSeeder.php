<?php

namespace Database\Seeders;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CartsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // En az 10 tane sepet oluştur
        $users = User::all();

        for ($i = 0; $i < 10; $i++) {
            DB::table('carts')->insert([
                'user_id' => $users->random()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
