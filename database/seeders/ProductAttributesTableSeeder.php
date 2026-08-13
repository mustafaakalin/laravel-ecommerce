<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductAttributesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attributes = [
            ['name' => 'Renk', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Beden', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Malzeme', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kilogram', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Boyut', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Marka', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Desen', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tarih', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Garanti Süresi', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Enerji Sınıfı', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('product_attributes')->insert($attributes);
    }
}
