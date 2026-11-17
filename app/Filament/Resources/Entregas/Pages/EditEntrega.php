<?php

namespace App\Filament\Resources\Entregas\Pages;

use App\Filament\Resources\Entregas\EntregaResource;
use App\Models\Entrega;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEntrega extends EditRecord
{
    protected static string $resource = EntregaResource::class;

    protected function beforeSave(): void
    {
        abort_unless(
            auth()->user()?->can('entregas.update'),
            403
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (): bool =>
                    auth()->user()?->can('entregas.delete') ?? false
                )
                ->before(function (Entrega $record): void {
                    abort_unless(
                        auth()->user()?->can('entregas.delete'),
                        403
                    );
                })
                ->modalHeading('Eliminar entrega')
                ->modalDescription(
                    'Los rollos entregados volverán al stock del código.'
                )
                ->successNotificationTitle(
                    'Entrega eliminada, el stock fue devuelto'
                ),
        ];
    }
}
