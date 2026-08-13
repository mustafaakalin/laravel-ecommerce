<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $categories = Category::where("is_active",true)->get();

        $brands = Brand::where("is_active",true)->get();

        for ($i = 0; $i < 50; $i++) {
            DB::table('products')->insert([
                'name' => $faker->words(3, true),
                'slug' => Str::slug($faker->words(3, true)),
                'description' => $faker->paragraph,
                'price' => $faker->randomFloat(2, 10, 1000),
                'stock' => $faker->numberBetween(1, 100),
                'category_id' => $categories->random()->id, // Kategori ID'leri 1 ile 10 arasında varsayılıyor
                'brand_id' => $brands->random()->id, // Marka ID'leri 1 ile 10 arasında varsayılıyor
                'old_price' => $faker->randomFloat(2, 10, 1000),
                'is_active' => $faker->boolean,
                'is_featured' => $faker->boolean,
                'is_new' => $faker->boolean,
                'discount' => $faker->numberBetween(0, 50),
                'rating' => $faker->randomFloat(2, 0, 5),
                'meta_title' => $faker->sentence,
                'meta_description' => $faker->paragraph,
                'meta_keywords' => $faker->words(5, true),
                'search_keywords' => $faker->words(5, true),
                'is_digital' => $faker->boolean,
                'view_count' => $faker->numberBetween(0, 1000),
                'specifications' => json_encode([
                    'weight' => $faker->randomFloat(2, 0.1, 10),
                    'dimensions' => $faker->randomFloat(2, 1, 100) . 'x' . $faker->randomFloat(2, 1, 100) . 'x' . $faker->randomFloat(2, 1, 100),
                ]),
                'sku' => Str::random(10),
                'is_free_shipping' => $faker->boolean,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
