<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShipmentDiscountsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('slider_for_homepages')->insert([
            'title' => 'Yeni Sezonun İlk Adımları Burada Başlıyor!',
            'description' => 'Yeni sezonun en güzel parçaları ile tanışmaya hazır mısınız? İşte karşınızda yeni sezonun en güzel parçaları!',
            'image' => null,
            'button_text' => 'Keşfet',
            'button_link' => 'https://www.akalin.tech',
            'position' => 'left',
            'link' => 'https://www.akalin.tech',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => null,
        ]);

    }
}
