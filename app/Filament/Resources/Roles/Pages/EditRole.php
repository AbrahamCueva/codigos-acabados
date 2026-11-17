<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected array $permisosSeleccionados = [];

    protected array $permisosAnteriores = [];

    protected string $nombreAnterior = '';

    /**
     * Cargar los permisos actuales del rol.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $grupos = [
            'codigos_permissions' => [
                'codigos.viewAny',
                'codigos.create',
                'codigos.update',
                'codigos.update_stock',
                'codigos.delete',
            ],
            'entregas_permissions' => [
                'entregas.viewAny',
                'entregas.create',
                'entregas.update',
                'entregas.delete',
            ],
            'trabajadores_permissions' => [
                'trabajadores.viewAny',
                'trabajadores.create',
                'trabajadores.update',
                'trabajadores.delete',
            ],
            'actividades_permissions' => [
                'actividades.viewAny',
            ],
        ];

        foreach ($grupos as $campo => $permisos) {
            $data[$campo] = $this->record
                ->permissions()
                ->where('guard_name', 'web')
                ->whereIn('name', $permisos)
                ->pluck('name')
                ->all();
        }

        return $data;
    }

    protected function beforeSave(): void
    {
        abort_unless(
            auth()->user()?->hasRole('administrador'),
            403
        );

        if (
            $this->record->name === 'administrador'
            && $this->data['name'] !== 'administrador'
        ) {
            abort(403, 'No está permitido cambiar el nombre del rol administrador.');
        }

        $this->nombreAnterior = $this->record->name;

        $this->permisosAnteriores = $this->record
            ->permissions()
            ->where('guard_name', 'web')
            ->pluck('name')
            ->all();
    }

    /**
     * Recopilar los permisos seleccionados.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->permisosSeleccionados = array_merge(
            $data['codigos_permissions'] ?? [],
            $data['entregas_permissions'] ?? [],
            $data['trabajadores_permissions'] ?? [],
            $data['actividades_permissions'] ?? [],
        );

        unset(
            $data['codigos_permissions'],
            $data['entregas_permissions'],
            $data['trabajadores_permissions'],
            $data['actividades_permissions'],
        );

        return $data;
    }

    /**
     * Sincronizar y registrar los cambios.
     */
    protected function afterSave(): void
    {
        // El administrador conserva todos sus permisos.
        if ($this->record->name === 'administrador') {
            return;
        }

        $this->record->syncPermissions($this->permisosSeleccionados);

        $permisosAgregados = array_values(
            array_diff($this->permisosSeleccionados, $this->permisosAnteriores)
        );

        $permisosRetirados = array_values(
            array_diff($this->permisosAnteriores, $this->permisosSeleccionados)
        );

        $nombreNuevo = $this->record->name;

        if (
            $this->nombreAnterior !== $nombreNuevo
            || !empty($permisosAgregados)
            || !empty($permisosRetirados)
        ) {
            activity('roles')
                ->causedBy(auth()->user())
                ->performedOn($this->record)
                ->withProperties([
                    'snapshot' => [
                        'name' => [
                            'old' => $this->nombreAnterior,
                            'attributes' => $nombreNuevo,
                        ],
                        'permissions' => [
                            'old' => $this->permisosAnteriores,
                            'attributes' => $this->permisosSeleccionados,
                            'added' => $permisosAgregados,
                            'removed' => $permisosRetirados,
                        ],
                    ],
                ])
                ->log('Rol actualizado');
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(
                    fn(): bool =>
                        auth()->user()?->hasRole('administrador') ?? false
                )
                ->disabled(
                    fn($record): bool =>
                        $record->name === 'administrador'
                        || $record->users()->exists()
                )
                ->tooltip(fn($record): ?string => match (true) {
                    $record->name === 'administrador' =>
                        'El rol administrador está protegido.',
                    $record->users()->exists() =>
                        'No puedes eliminar un rol que tiene usuarios asignados.',
                    default => null,
                })
                ->before(function ($record): void {
                    activity('roles')
                        ->causedBy(auth()->user())
                        ->performedOn($record)
                        ->withProperties([
                            'snapshot' => [
                                'name' => $record->name,
                                'permissions' => $record->permissions()
                                    ->pluck('name')
                                    ->all(),
                            ],
                        ])
                        ->log('Rol eliminado');
                }),
        ];
    }
}
