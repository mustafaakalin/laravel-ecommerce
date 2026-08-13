<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Faker\Factory as Faker;

class ProductImagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $faker = Faker::create();
        // $products = Product::all();
        
        // for ($i = 0; $i < 50; $i++) {
        //     DB::table('product_images')->insert([
        //         'product_id' => $products->random()->id,
        //         'image_path' => "default_product_image.jpg",
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ]);
        // }
    }
}
