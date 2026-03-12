<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $manageUsers = Permission::firstOrCreate(['name' => 'manage users', 'guard_name' => 'web']);

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->givePermissionTo($manageUsers);

        Role::firstOrCreate(['name' => 'operador', 'guard_name' => 'web']);
        // Operador: CRUD activos e import/export; sin gestión de usuarios (no givePermissionTo manage users)
    }
}
