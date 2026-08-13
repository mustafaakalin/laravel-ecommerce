<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Product;
use Faker\Factory as Faker;

class CommentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $users = User::all();
        $products = Product::all();

        for ($i = 0; $i < 50; $i++) {
            DB::table('comments')->insert([
                'user_id' => $users->random()->id,
                'product_id' => $products->random()->id,
                'content' => $faker->paragraph,
                'rating' => $faker->numberBetween(1, 5),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
