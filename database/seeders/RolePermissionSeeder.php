<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       
        $manager = Role::firstOrCreate(
            ['name' => 'manager', 'guard_name' => 'api']
        );
    
        $user = Role::firstOrCreate(
            ['name' => 'user', 'guard_name' => 'api']
        );
    
        $permissions = [
            'tasks.create',
            'tasks.update',
            'tasks.assign',
            'tasks.view.own',
            'tasks.update.own.status',
        ];
    
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm, 'guard_name' => 'api']
            );
        }
    
       
        $manager->givePermissionTo(['tasks.create', 'tasks.update', 'tasks.assign']);
        $user->givePermissionTo(['tasks.view.own', 'tasks.update.own.status']);
    }
}
