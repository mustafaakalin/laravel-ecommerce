<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pages')->insert([
            [
                'name' => 'Hakkımızda',
                'content' => 'Hakkımızda sayfasının içeriği buraya gelecek.',
            ],
            [
                'name' => 'Sıkça Sorulan Sorular',
                'content' => 'Sıkça Sorulan Sorular sayfasının içeriği buraya gelecek.',
            ],
            [
                'name' => 'İletişim',
                'content' => 'İletişim sayfasının içeriği buraya gelecek.',
            ],
        ]);
    }
}
