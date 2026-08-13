<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Spatie\Permission\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    private array $permissions = [
        'product:list',
        'product:create',
        'product:edit',
        'product:delete',
        'product:purchase',
        'cart:view',
        'cart:add',
        'cart:update',
        'cart:remove',
        'cart:checkout',
        // Add more permissions as needed
    ];

    private array $guards = ['api', 'web'];

    public function run(): void
    {
        // Create permissions for each guard
        foreach ($this->guards as $guard) {
            foreach ($this->permissions as $permission) {
                Permission::firstOrCreate([
                    'name' => $permission,
                    'guard_name' => $guard
                ]);
            }
            $this->command->info("Created permissions for {$guard} guard");
        }
    }
}
