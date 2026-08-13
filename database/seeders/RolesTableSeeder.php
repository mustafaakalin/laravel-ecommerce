<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $roles = [
            ['name' => 'admin', 'guard_name' => 'web'],
            ['name' => 'user', 'guard_name' => 'web'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insert([
                'name' => $role['name'],
                'guard_name' => $role['guard_name'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
