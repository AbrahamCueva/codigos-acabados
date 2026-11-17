<?php

namespace App\Filament\Resources\Codigos\Pages;

use App\Filament\Resources\Codigos\CodigoResource;
use App\Models\Codigo;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCodigo extends EditRecord
{
    protected static string $resource = CodigoResource::class;

    protected function beforeSave(): void
    {
        $usuario = auth()->user();

        abort_unless($usuario, 403);

        $codigoOriginal = $this->record->getOriginal('codigo');
        $stockOriginal = $this->record->getOriginal('stock');

        $codigoNuevo = $this->data['codigo'] ?? $codigoOriginal;
        $stockNuevo = $this->data['stock'] ?? $stockOriginal;

        // Verificar cambios en el código.
        if ($codigoNuevo != $codigoOriginal) {
            abort_unless(
                $usuario->can('codigos.update'),
                403,
                'No tienes permiso para modificar el código.'
            );
        }

        // Verificar cambios en el stock.
        if ($stockNuevo != $stockOriginal) {
            abort_unless(
                $usuario->can('codigos.update_stock'),
                403,
                'No tienes permiso para modificar el stock.'
            );
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (): bool =>
                    auth()->user()?->can('codigos.delete') ?? false
                )
                ->disabled(fn (Codigo $record): bool =>
                    $record->tieneEntregas()
                )
                ->tooltip(fn (Codigo $record): ?string =>
                    $record->tieneEntregas()
                        ? 'Tiene entregas registradas, no se puede eliminar. Pon el stock en 0 en su lugar.'
                        : null
                )
                ->before(function (Codigo $record): void {
                    abort_unless(
                        auth()->user()?->can('codigos.delete'),
                        403
                    );

                    abort_if(
                        $record->tieneEntregas(),
                        403,
                        'No se puede eliminar un código con entregas registradas.'
                    );
                })
                ->modalDescription('Esta acción no se puede deshacer.')
                ->successNotificationTitle('Código eliminado')
                ->failureNotificationTitle('No se pudo eliminar: el código tiene entregas registradas.'),
        ];
    }
}
