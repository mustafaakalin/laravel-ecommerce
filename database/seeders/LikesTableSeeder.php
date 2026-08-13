<?php

namespace Database\Seeders;

use App\Models\Like;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class LikesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tüm kullanıcıları ve ürünleri al
        $users = User::all();
        $products = Product::all();

        // Her ürünün tüm kullanıcılar tarafından beğenildiğini sağla
        foreach ($products as $product) {
            foreach ($users as $user) {
                Like::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                ]);
            }
        }
    }
}
