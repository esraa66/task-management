<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create roles
        $managerRole = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'api']);
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'api']);

        // Create permissions
        $permissions = [
            'view-any-tasks',
            'view-task',
            'create-task',
            'update-task',
            'delete-task',
            'assign-task',
            'manage-dependencies',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'api']);
        }

        // Assign permissions to roles
        $managerRole->givePermissionTo($permissions);
        $userRole->givePermissionTo(['view-task', 'update-task']);
    }
}