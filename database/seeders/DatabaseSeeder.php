<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\FaqTableSeeder;
use Database\Seeders\CartsTableSeeder;
use Database\Seeders\LikesTableSeeder;
use Database\Seeders\RolesTableSeeder;
use Database\Seeders\UsersTableSeeder;
use Database\Seeders\BrandsTableSeeder;
use Database\Seeders\DefaultDataSeeder;
use Database\Seeders\OrdersTableSeeder;
use Database\Seeders\CouponsTableSeeder;
use Database\Seeders\CommentsTableSeeder;
use Database\Seeders\ProductsTableSeeder;
use Database\Seeders\SoldoutsTableSeeder;
use Database\Seeders\AddressesTableSeeder;
use Database\Seeders\CampaignsTableSeeder;
use Database\Seeders\CartItemsTableSeeder;
use Database\Seeders\ShipmentsTableSeeder;
use Database\Seeders\CategoriesTableSeeder;
use Database\Seeders\OrderItemsTableSeeder;
use Database\Seeders\PermissionsTableSeeder;
use Database\Seeders\SiteSettingsTableSeeder;
use Database\Seeders\SliderForHomepageSeeder;
use Database\Seeders\TestimonialsTableSeeder;
use Database\Seeders\ProductImagesTableSeeder;
use Database\Seeders\AddPermissionToRolesSeeder;
use Database\Seeders\CampaignProductsTableSeeder;
use Database\Seeders\ProductAttributesTableSeeder;
use Database\Seeders\ShipmentDiscountsTableSeeder;
use Database\Seeders\ProductAttributeValuesTableSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // User::factory(10)->create();


        $this->call([
            AddPermissionToRolesSeeder::class,
            DefaultDataSeeder::class,
            SliderForHomepageSeeder::class,
            // PermissionsTableSeeder::class,
            // RolesTableSeeder::class,
            UsersTableSeeder::class,
            TagTableSeeder::class,
            CategoriesTableSeeder::class,
            BrandsTableSeeder::class,
            ProductsTableSeeder::class,
                // ProductImagesTableSeeder::class,
            CouponsTableSeeder::class,
            CartsTableSeeder::class,
            CartItemsTableSeeder::class,
            TestimonialsTableSeeder::class,
            CommentsTableSeeder::class,
            LikesTableSeeder::class,
            ProductAttributesTableSeeder::class,
            ProductAttributeValuesTableSeeder::class,
            AddressesTableSeeder::class,
                // SoldoutsTableSeeder::class,
            // SiteSettingsTableSeeder::class,
            CampaignsTableSeeder::class,
            CampaignProductsTableSeeder::class,
            OrdersTableSeeder::class,
            OrderItemsTableSeeder::class,
            ShipmentsTableSeeder::class,
            PagesTableSeeder::class,
            FaqTableSeeder::class,
            ShipmentDiscountsTableSeeder::class,
            // Diğer seeder dosyalarını buraya ekleyebilirsiniz.
        ]);



        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        //     'password' => bcrypt('tEST@123!#Ckuf2mqQUnTm8p1FjXR$y7XS6cR!J*C0xfgV9Z#DR%CMTaG8BVx8J9*NDU^#u*4V'), // Strong password with mix of characters
        // ])->assignRole('user');

        // or


        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        //     'password' => bcrypt(env('TEST_PASSWORD', 'test@123!#RdZ4VZ0J6*CBYwR7PGuhrWptCB$P*CDYzSr3x!7#CxX^&@^^7CA5!JE6jgwJ96bW')),
        // ])->assignRole('user');



        // Create test user with proper roles
        $testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt(env('TEST_PASSWORD', 'test@123!#RdZ4VZ0J6*CBYwR7PGuhrWptCB$P*CDYzSr3x!7#CxX^&@^^7CA5!JE6jgwJ96bW'))
        ]);

        // Assign both web and api guards role
        $testUser->assignRole('user');

        // Create admin user
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt(env('ADMIN_PASSWORD', 'Admin@123!#RdZ4VZ0J6*CBYwR7PGuhrWptCB$P*CDYzSr3x!7#CxX^&@^^7CA5!JE6jgwJ96bW'))
        ]);

        // Assign both web and api guards role
        $admin->assignRole('admin');


        // User::factory()->create([
        //     'name' => 'Admin User',
        //     'email' => 'admin@example.com',
        //     'password' => bcrypt('Admin@123!#'), // Strong password with mix of characters
        // ])->assignRole('admin');

        // or

        // User::factory()->create([
        //     'name' => 'Admin User',
        //     'email' => 'admin@example.com',
        //     'password' => bcrypt(env('ADMIN_PASSWORD', 'Admin@123!#RdZ4VZ0J6*CBYwR7PGuhrWptCB$P*CDYzSr3x!7#CxX^&@^^7CA5!JE6jgwJ96bW')),
        // ])->assignRole('admin');
    }
}
