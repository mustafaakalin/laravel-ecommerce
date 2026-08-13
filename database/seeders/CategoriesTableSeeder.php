<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategoriesTableSeeder extends Seeder
{

    // Define available Font Awesome icons
    private $fontAwesomeIcons = [
        'fas fa-home',
        'fas fa-user',
        'fas fa-shopping-cart',
        'fas fa-book',
        'fas fa-star',
        'fas fa-heart',
        'fas fa-music',
        'fas fa-camera',
        'fas fa-envelope',
        'fas fa-phone',
        'fas fa-cog',
        'fas fa-calendar',
        'fas fa-search',
        'fas fa-bell',
        'fas fa-map-marker'
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();






        // Önce ana kategoriler oluşturuluyor
        $mainCategories = [];
        for ($i = 1; $i <= 10; $i++) {
            $category = Category::create([
                'name' => $faker->word,
                'slug' => Str::slug($faker->unique()->word),
                'icon' => $this->fontAwesomeIcons[array_rand($this->fontAwesomeIcons)],
                'description' => $faker->sentence,
                'products_count' => $faker->numberBetween(0, 100),
                'is_active' => $faker->boolean,
                'sort_order' => $faker->numberBetween(1, 100),
            ]);

            $mainCategories[] = $category->id;
        }

        // Alt kategoriler oluşturuluyor
        for ($i = 11; $i <= 30; $i++) {
            Category::create([
                'name' => $faker->word,
                'slug' => Str::slug($faker->unique()->word),
                'icon' => $this->fontAwesomeIcons[array_rand($this->fontAwesomeIcons)],
                'image' => $faker->imageUrl(640, 480, 'cats', true),
                'description' => $faker->sentence,
                'products_count' => $faker->numberBetween(0, 100),
                'is_active' => $faker->boolean,
                'sort_order' => $faker->numberBetween(1, 100),
                'parent_id' => $faker->randomElement($mainCategories),
            ]);
        }
    }
}
