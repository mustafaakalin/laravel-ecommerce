<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Faker\Factory as Faker;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 0; $i < 10; $i++) {
            User::create([
                'name' => $faker->firstName,
                'surname' => $faker->lastName,
                'email' => $faker->unique()->safeEmail,
                'email_verified_at' => now(),
                'password' => Hash::make('password'), // Default password
                'identity_number' => $faker->unique()->numerify('###########'),
                'avatar' => "", // Placeholder avatar
                'instagram_account' => $faker->userName,
                'facebook_account' => $faker->userName,
                'tiktok_account' => $faker->userName,
                'x_account' => $faker->userName,
                'phone' => $faker->phoneNumber,
                'remember_token' => Str::random(10),
            ])->assignRole('user');
        }
    }
}
