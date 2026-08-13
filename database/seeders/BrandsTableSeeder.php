<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BrandsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            ['name' => 'Apple', 'slug' => 'apple', 'logo' => 'brand_apple.png', 'description' => 'Apple Inc. is an American multinational technology company.', 'is_active' => true],
            ['name' => 'Samsung', 'slug' => 'samsung', 'logo' => 'brand_samsung.png', 'description' => 'Samsung is a South Korean multinational conglomerate.', 'is_active' => true],
            ['name' => 'Google', 'slug' => 'google', 'logo' => 'brand_google.png', 'description' => 'Google LLC is an American multinational technology company.', 'is_active' => true],
            ['name' => 'Microsoft', 'slug' => 'microsoft', 'logo' => 'brand_microsoft.png', 'description' => 'Microsoft Corporation is an American multinational technology company.', 'is_active' => true],
            ['name' => 'Amazon', 'slug' => 'amazon', 'logo' => 'brand_amazon.png', 'description' => 'Amazon.com, Inc. is an American multinational technology company.', 'is_active' => true],
            ['name' => 'Facebook', 'slug' => 'facebook', 'logo' => 'brand_facebook.png', 'description' => 'Facebook, Inc. is an American multinational technology conglomerate.', 'is_active' => true],
            ['name' => 'Tesla', 'slug' => 'tesla', 'logo' => 'brand_tesla.png', 'description' => 'Tesla, Inc. is an American electric vehicle and clean energy company.', 'is_active' => true],
            ['name' => 'Nike', 'slug' => 'nike', 'logo' => 'brand_nike.png', 'description' => 'Nike, Inc. is an American multinational corporation.', 'is_active' => true],
            ['name' => 'Adidas', 'slug' => 'adidas', 'logo' => 'brand_adidas.png', 'description' => 'Adidas AG is a German multinational corporation.', 'is_active' => true],
            ['name' => 'Coca-Cola', 'slug' => 'coca-cola', 'logo' => 'brand_cocacola.png', 'description' => 'The Coca-Cola Company is an American multinational beverage corporation.', 'is_active' => true],
            ['name' => 'Pepsi', 'slug' => 'pepsi', 'logo' => 'brand_pepsi.png', 'description' => 'PepsiCo, Inc. is an American multinational food, snack, and beverage corporation.', 'is_active' => true],
            ['name' => 'McDonald\'s', 'slug' => 'mcdonalds', 'logo' => 'brand_mcdonalds.png', 'description' => 'McDonald\'s Corporation is an American fast food company.', 'is_active' => true],
            ['name' => 'Starbucks', 'slug' => 'starbucks', 'logo' => 'brand_starbucks.png', 'description' => 'Starbucks Corporation is an American multinational chain of coffeehouses.', 'is_active' => true],
            ['name' => 'Walmart', 'slug' => 'walmart', 'logo' => 'brand_walmart.png', 'description' => 'Walmart Inc. is an American multinational retail corporation.', 'is_active' => true],
            ['name' => 'IBM', 'slug' => 'ibm', 'logo' => 'brand_ibm.png', 'description' => 'International Business Machines Corporation is an American multinational technology company.', 'is_active' => true],
            ['name' => 'Intel', 'slug' => 'intel', 'logo' => 'brand_intel.png', 'description' => 'Intel Corporation is an American multinational corporation and technology company.', 'is_active' => true],
            ['name' => 'HP', 'slug' => 'hp', 'logo' => 'brand_hp.png', 'description' => 'HP Inc. is an American multinational information technology company.', 'is_active' => true],
            ['name' => 'Dell', 'slug' => 'dell', 'logo' => 'brand_dell.png', 'description' => 'Dell Technologies Inc. is an American multinational technology company.', 'is_active' => true],
            ['name' => 'Sony', 'slug' => 'sony', 'logo' => 'brand_sony.png', 'description' => 'Sony Corporation is a Japanese multinational conglomerate corporation.', 'is_active' => true],
            ['name' => 'LG', 'slug' => 'lg', 'logo' => 'brand_lg.png', 'description' => 'LG Corporation is a South Korean multinational conglomerate corporation.', 'is_active' => true],
        ];

        foreach ($brands as $brand) {
            DB::table('brands')->insert([
                'name' => $brand['name'],
                'slug' => $brand['slug'],
                'logo' => $brand['logo'],
                'description' => $brand['description'],
                'is_active' => $brand['is_active'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
