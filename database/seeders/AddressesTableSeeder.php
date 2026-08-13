<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Address;
use Faker\Factory as Faker;

class AddressesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $users = User::all();

        foreach ($users as $user) {
            $addressCount = $faker->numberBetween(1, 5); // Her kullanıcı için 1 ile 5 arasında adres oluştur

            for ($i = 0; $i < $addressCount; $i++) {
                Address::create([
                    'user_id' => $user->id,
                    'title' => $faker->optional()->word,
                    'first_name' => $faker->firstName,
                    'last_name' => $faker->lastName,
                    'phone' => $faker->phoneNumber,
                    'address' => $faker->address,
                    'city' => $faker->city,
                    'state' => $faker->state,
                    'country' => $faker->country,
                    'zip_code' => $faker->postcode,
                    'is_default' => $faker->boolean(10), // %10 olasılıkla varsayılan adres olarak işaretle
                    'company_name' => $faker->company(),
                    'tax_number' => $faker->optional()->numerify('##########'),
                    'tax_office' => $faker->optional()->word,
                ]);
            }
        }
    }
}
