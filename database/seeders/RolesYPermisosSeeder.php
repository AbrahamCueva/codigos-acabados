<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesYPermisosSeeder extends Seeder
{
    public function run(): void
    {
        $registrar = app(PermissionRegistrar::class);

        $registrar->forgetCachedPermissions();

        // Permisos del sistema.
        $permisos = [
            'codigos.viewAny',
            'codigos.create',
            'codigos.update',
            'codigos.update_stock',
            'codigos.delete',

            'trabajadores.viewAny',
            'trabajadores.create',
            'trabajadores.update',
            'trabajadores.delete',

            'entregas.viewAny',
            'entregas.create',
            'entregas.update',
            'entregas.delete',

            'actividades.viewAny',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate([
                'name' => $permiso,
                'guard_name' => 'web',
            ]);
        }

        // Crear roles sin sobrescribir los existentes.
        $administrador = Role::firstOrCreate([
            'name' => 'administrador',
            'guard_name' => 'web',
        ]);

        $supervisor = Role::firstOrCreate([
            'name' => 'supervisor',
            'guard_name' => 'web',
        ]);

        $operario = Role::firstOrCreate([
            'name' => 'operario',
            'guard_name' => 'web',
        ]);

        // Permisos iniciales del administrador.
        if ($administrador->wasRecentlyCreated) {
            $administrador->syncPermissions($permisos);
        }

        // Permisos iniciales del supervisor.
        if ($supervisor->wasRecentlyCreated) {
            $supervisor->syncPermissions([
                'codigos.viewAny',
                'codigos.create',
                'codigos.update',
                'codigos.update_stock',

                'trabajadores.viewAny',
                'trabajadores.create',
                'trabajadores.update',

                'entregas.viewAny',
                'entregas.create',
                'entregas.update',
                'entregas.delete',

                'actividades.viewAny',
            ]);
        }

        // Permisos iniciales del operario.
        if ($operario->wasRecentlyCreated) {
            $operario->syncPermissions([
                'codigos.viewAny',
                'trabajadores.viewAny',
                'entregas.viewAny',
                'entregas.create',
            ]);
        }

        $registrar->forgetCachedPermissions();
    }
}
