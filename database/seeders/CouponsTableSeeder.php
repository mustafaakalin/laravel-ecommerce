<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class CouponsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 0; $i < 10; $i++) {
            DB::table('coupons')->insert([
                'code' => Str::random(10),
                'type' => $faker->randomElement(['fixed', 'percentage']),
                'value' => $faker->numberBetween(1, 100),
                'usage_limit' => $faker->optional()->numberBetween(1, 100),
                'used_count' => 0,
                'starts_at' => $faker->optional()->dateTimeBetween('-1 year', 'now'),
                'expires_at' => $faker->optional()->dateTimeBetween('now', '+1 year'),
                'is_active' => $faker->boolean,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
