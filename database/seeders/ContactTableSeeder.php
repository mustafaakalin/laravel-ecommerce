<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Faker\Factory as Faker;

class ContactTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $faker = Faker::create();

        for ($i = 1; $i <= 30; $i++) {
            DB::table('contacts')->insert([
                'name' => $faker->name,
                'email' => $faker->safeEmail,
                'phone' => $faker->phoneNumber,
                'message' => $faker->paragraph,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
