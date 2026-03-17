<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(PermissionSeeder::class);

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions(PermissionSeeder::todosLosNombres());

        $usuario = Role::firstOrCreate(['name' => 'usuario', 'guard_name' => 'web']);
        $usuario->syncPermissions(PermissionSeeder::permisosUsuario());

        $operador = Role::firstOrCreate(['name' => 'operador', 'guard_name' => 'web']);
        $operador->syncPermissions(PermissionSeeder::permisosOperador());

        User::all()->each(function (User $user) {
            if ($user->roles()->count() === 0) {
                $user->assignRole('usuario');
            }
        });
    }
}
