<?php

namespace App\Filament\Resources\Trabajadors\Pages;

use App\Filament\Resources\Trabajadors\TrabajadorResource;
use App\Models\Trabajador;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTrabajador extends EditRecord
{
    protected static string $resource = TrabajadorResource::class;

    protected function beforeSave(): void
    {
        abort_unless(
            auth()->user()?->can('trabajadores.update'),
            403
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (): bool =>
                    auth()->user()?->can('trabajadores.delete') ?? false
                )
                ->disabled(fn (Trabajador $record): bool =>
                    $record->tieneEntregas()
                )
                ->tooltip(fn (Trabajador $record): ?string =>
                    $record->tieneEntregas()
                        ? 'Tiene entregas registradas, no se puede eliminar. Desactívalo en su lugar.'
                        : null
                )
                ->before(function (Trabajador $record): void {
                    abort_unless(
                        auth()->user()?->can('trabajadores.delete'),
                        403
                    );

                    abort_if(
                        $record->tieneEntregas(),
                        403,
                        'No se puede eliminar un trabajador con entregas registradas.'
                    );
                })
                ->modalDescription('Esta acción no se puede deshacer.')
                ->successNotificationTitle('Trabajador eliminado')
                ->failureNotificationTitle(
                    'No se pudo eliminar: el trabajador tiene entregas registradas.'
                ),
        ];
    }
}
