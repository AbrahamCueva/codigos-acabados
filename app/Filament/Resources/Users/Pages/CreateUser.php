<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function beforeCreate(): void
    {
        abort_unless(
            auth()->user()?->hasRole('administrador'),
            403
        );
    }

    protected function afterCreate(): void
    {
        $this->recordRoleActivity(
            'Rol asignado al crear usuario',
            [],
            $this->record->fresh()->getRoleNames()->all()
        );
    }

    private function recordRoleActivity(
        string $description,
        array $oldRoles,
        array $newRoles
    ): void {
        activity('usuarios')
            ->causedBy(auth()->user())
            ->performedOn($this->record)
            ->withProperties([
                'snapshot' => [
                    'roles' => [
                        'old' => $oldRoles,
                        'attributes' => $newRoles,
                    ],
                ],
            ])
            ->log($description);
    }
}
