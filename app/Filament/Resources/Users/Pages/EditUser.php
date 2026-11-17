<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Spatie\Permission\Models\Role;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected array $rolesOriginales = [];

    protected function beforeSave(): void
    {
        abort_unless(
            auth()->user()?->hasRole('administrador'),
            403
        );

        $this->rolesOriginales = $this->record
            ->getRoleNames()
            ->all();

        // El Select múltiple entrega los ID de los roles.
        $rolesNuevosIds = array_map(
            'intval',
            (array) ($this->data['roles'] ?? [])
        );

        $rolesNuevos = Role::query()
            ->where('guard_name', 'web')
            ->whereKey($rolesNuevosIds)
            ->pluck('name')
            ->all();

        $conservaAdministrador = in_array(
            'administrador',
            $rolesNuevos,
            true
        );

        if (
            $this->record->hasRole('administrador')
            && ! $conservaAdministrador
        ) {
            // No permitir quitarse el propio rol administrativo.
            if ($this->record->is(auth()->user())) {
                $this->addError(
                    'data.roles',
                    'No puedes quitarte tu propio rol de administrador.'
                );

                $this->halt();
            }

            // Proteger al administrador principal.
            if (
                $this->record->id === UserResource::ADMINISTRADOR_PRINCIPAL_ID
            ) {
                $this->addError(
                    'data.roles',
                    'No puedes retirar el rol al administrador principal.'
                );

                $this->halt();
            }

            // Proteger al último administrador.
            if (User::role('administrador')->count() <= 1) {
                $this->addError(
                    'data.roles',
                    'No puedes quitar el rol al último administrador del sistema.'
                );

                $this->halt();
            }
        }
    }

    protected function afterSave(): void
    {
        $rolesNuevos = $this->record->fresh()
            ->getRoleNames()
            ->all();

        sort($this->rolesOriginales);
        sort($rolesNuevos);

        if ($this->rolesOriginales !== $rolesNuevos) {
            activity('usuarios')
                ->causedBy(auth()->user())
                ->performedOn($this->record)
                ->withProperties([
                    'snapshot' => [
                        'roles' => [
                            'old' => $this->rolesOriginales,
                            'attributes' => $rolesNuevos,
                        ],
                    ],
                ])
                ->log('Roles del usuario modificados');
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (User $record): bool =>
                    UserResource::canDelete($record)
                )
                ->before(function (User $record): void {
                    abort_unless(
                        UserResource::canDelete($record),
                        403
                    );

                    activity('usuarios')
                        ->causedBy(auth()->user())
                        ->performedOn($record)
                        ->withProperties([
                            'snapshot' => [
                                'name' => $record->name,
                                'email' => $record->email,
                                'roles' => $record->getRoleNames()->all(),
                            ],
                        ])
                        ->log('Usuario eliminado');
                })
                ->modalHeading('Eliminar usuario')
                ->modalDescription(
                    'Esta acción no se puede deshacer.'
                )
                ->successNotificationTitle('Usuario eliminado'),
        ];
    }
}
