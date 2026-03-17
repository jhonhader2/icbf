<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

/**
 * Define permisos por módulo y acción.
 * Convención: {recurso}.{acción} (view, create, update, delete, import, export, etc.).
 */
class PermissionSeeder extends Seeder
{
    private const GUARD = 'web';

    /** Módulos y acciones que generan permisos. */
    private const MATRIZ = [
        'dashboard' => ['view'],
        'personas' => ['view', 'create', 'update', 'delete', 'import', 'crear-usuario'],
        'productos' => ['view', 'create', 'update', 'delete'],
        'activos_crv' => ['view', 'create', 'update', 'delete', 'import'],
        'cpus' => ['view', 'create', 'update', 'delete', 'import'],
        'monitores' => ['view', 'create', 'update', 'delete'],
        'teclados' => ['view', 'create', 'update', 'delete'],
        'mice' => ['view', 'create', 'update', 'delete'],
        'departments' => ['view', 'create', 'update', 'delete'],
        'regionales' => ['view', 'create', 'update', 'delete'],
        'bodegas' => ['view', 'create', 'update', 'delete'],
        'roles' => ['view', 'create', 'update', 'delete'],
        'permissions' => ['view', 'create'],
        'parque' => ['export'],
        'tvm' => ['import', 'resolve'],
    ];

    public function run(): void
    {
        foreach (self::MATRIZ as $modulo => $acciones) {
            foreach ($acciones as $accion) {
                $name = "{$modulo}.{$accion}";
                Permission::firstOrCreate(
                    ['name' => $name, 'guard_name' => self::GUARD]
                );
            }
        }
    }

    /** Devuelve todos los nombres de permisos creados (para asignar al rol admin). */
    public static function todosLosNombres(): array
    {
        $nombres = [];
        foreach (self::MATRIZ as $modulo => $acciones) {
            foreach ($acciones as $accion) {
                $nombres[] = "{$modulo}.{$accion}";
            }
        }
        return $nombres;
    }

    /** Permisos que tendrá el rol usuario (solo consulta; el controlador restringe a sus datos). */
    public static function permisosUsuario(): array
    {
        return [
            'dashboard.view',
            'personas.view',
            'activos_crv.view',
            'cpus.view',
            'monitores.view',
            'productos.view',
        ];
    }

    /** Permisos para el rol operador: todos excepto delete. */
    public static function permisosOperador(): array
    {
        return array_values(array_filter(
            self::todosLosNombres(),
            fn (string $name) => ! str_ends_with($name, '.delete')
        ));
    }
}
