<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Campaign;
use App\Models\Product;
use Faker\Factory as Faker;

class CampaignProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Campaign ve Product modellerinden kayıtları al
        $campaigns = Campaign::all();
        $products = Product::all();

        // En az 10 tane campaign_product kaydı oluştur
        for ($i = 0; $i < 10; $i++) {
            DB::table('campaign_product')->insert([
                'campaign_id' => $campaigns->random()->id,
                'product_id' => $products->random()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
