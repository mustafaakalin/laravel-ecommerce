<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class TestimonialsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 1; $i <= 10; $i++) {
            DB::table('testimonials')->insert([
                'author' => $faker->name,
                'position' => $faker->jobTitle,
                'content' => $faker->paragraph,
                'avatar' => "default_testimonial_{$i}.jpg",
                'rating' => $faker->numberBetween(1, 5),
                'is_active' => $faker->boolean,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
