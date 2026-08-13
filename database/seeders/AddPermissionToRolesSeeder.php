<?php

namespace Database\Seeders;


use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AddPermissionToRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    private array $permissions = [
        'address:create',
        'address:view',
        'address:update',
        'address:delete',
        'address:manage',
        'product:list',
        'product:create',
        'product:edit',
        'product:delete',
        'product:purchase',
        'product:manage',
        'cart:view',
        'cart:add',
        'cart:update',
        'cart:remove',
        'cart:checkout',
        'cart:manage',
        'profile:view',
        'profile:update',
        'order:view',
        'order:list',
    ];

    private array $userPermissions = [
        'address:create',
        'address:view',
        'address:update',
        'address:delete',
        'product:purchase',
        'cart:view',
        'cart:add',
        'cart:update',
        'cart:remove',
        'cart:checkout',
        'profile:view',
        'profile:update',
        'order:view',
        'order:list',
    ];

    private array $guards = ['api', 'web'];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear cache first
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        // Create permissions
        foreach ($this->permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign permissions to roles
        $adminRole->syncPermissions(Permission::where('guard_name', 'web')->get());
        $userRole->syncPermissions(Permission::where('guard_name', 'web')
            ->whereIn('name', $this->userPermissions)
            ->get());

        // Create API guard permissions and roles
        $adminRoleApi = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);
        $userRoleApi = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'api']);

        foreach ($this->permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'api']);
        }

        $adminRoleApi->syncPermissions(Permission::where('guard_name', 'api')->get());
        $userRoleApi->syncPermissions(Permission::where('guard_name', 'api')
            ->whereIn('name', $this->userPermissions)
            ->get());

        $this->command->info('Permissions seeded successfully');
    }

}
