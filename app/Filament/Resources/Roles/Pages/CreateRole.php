<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected array $permisosSeleccionados = [];

    protected function beforeCreate(): void
    {
        abort_unless(
            auth()->user()?->hasRole('administrador'),
            403
        );
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->permisosSeleccionados = array_merge(
            $data['codigos_permissions'] ?? [],
            $data['entregas_permissions'] ?? [],
            $data['trabajadores_permissions'] ?? [],
            $data['actividades_permissions'] ?? [],
        );

        $data['guard_name'] = 'web';

        unset(
            $data['codigos_permissions'],
            $data['entregas_permissions'],
            $data['trabajadores_permissions'],
            $data['actividades_permissions'],
        );

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->syncPermissions($this->permisosSeleccionados);

        activity('roles')
            ->causedBy(auth()->user())
            ->performedOn($this->record)
            ->withProperties([
                'snapshot' => [
                    'name' => [
                        'old' => null,
                        'attributes' => $this->record->name,
                    ],
                    'permissions' => [
                        'old' => [],
                        'attributes' => $this->permisosSeleccionados,
                    ],
                ],
            ])
            ->log('Rol creado');
    }
}
