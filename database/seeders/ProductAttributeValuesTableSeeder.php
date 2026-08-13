<?php

namespace Database\Seeders;

use App\Models\Product;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use App\Models\ProductAttribute;
use Illuminate\Support\Facades\DB;
use App\Models\ProductAttributeValue;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductAttributeValuesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Örnek ürün özellikleri
        $attributes = [
            'Renk' => ['Kırmızı', 'Mavi', 'Yeşil', 'Siyah', 'Beyaz'],
            'Beden' => ['S', 'M', 'L', 'XL', 'XXL'],
            'Malzeme' => ['Pamuk', 'Polyester', 'Deri', 'Keten', 'Naylon'],
            'Kilogram' => ['250 gram', '500 gram', '750 gram', '1 kg', '1.5 kg'],
            'Boyut' => ['20x30 cm', '30x40 cm', '40x50 cm', '50x60 cm', '60x70 cm'],
            'Marka' => ['Brand A', 'Brand B', 'Brand C', 'Brand D', 'Brand E'],
            'Desen' => ['Düz', 'Çizgili', 'Desenli', 'Puntolu', 'Kareli'],
            'Tarih' => ['2023-01-01', '2023-02-01', '2023-03-01', '2023-04-01', '2023-05-01'],
            'Garanti Süresi' => ['1 yıl', '2 yıl', '3 yıl', '4 yıl', '5 yıl'],
            'Enerji Sınıfı' => ['A', 'B', 'C', 'D', 'E'],
        ];

        // Örnek ürünler
        $products = Product::pluck('id')->toArray();

        // Örnek özellikler
        $productAttributes = ProductAttribute::pluck('id', 'name')->toArray();

        for ($i = 0; $i < 50; $i++) {
            $productId = $faker->randomElement($products);
            $attributeName = $faker->randomElement(array_keys($attributes));
            $attributeId = $productAttributes[$attributeName];
            $value = $faker->randomElement($attributes[$attributeName]);

            ProductAttributeValue::create([
                'product_id' => $productId,
                'attribute_id' => $attributeId,
                'value' => $value,
            ]);
        }
    }
}
