<?php

namespace Database\Seeders;

use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CampaignsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 0; $i < 10; $i++) {
            DB::table('campaigns')->insert([
                'name' => $faker->word,
                'slug' => Str::slug($faker->unique()->word),
                'description' => $faker->paragraph,
                'discount_type' => $faker->randomElement(['fixed', 'percentage']),
                'discount_value' => $faker->randomFloat(2, 1, 100),
                'start_date' => $faker->dateTimeBetween('-1 year', 'now'),
                'end_date' => $faker->dateTimeBetween('now', '+1 year'),
                'is_active' => $faker->boolean,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
