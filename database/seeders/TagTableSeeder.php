<?php

namespace Database\Seeders;

use App\Models\Tag;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;


class TagTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // tags table seeder , random unique tags create with factory faker
        $faker = Faker::create();

        for ($i = 0; $i < 50; $i++) {
            Tag::create([
                'name' => $faker->unique()->word,
            ]);
        }
    }
}
