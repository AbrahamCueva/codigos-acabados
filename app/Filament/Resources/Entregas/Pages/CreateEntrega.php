<?php

namespace App\Filament\Resources\Entregas\Pages;

use App\Filament\Resources\Entregas\EntregaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEntrega extends CreateRecord
{
    protected static string $resource = EntregaResource::class;

    protected function beforeCreate(): void
    {
        abort_unless(
            auth()->user()?->can('entregas.create'),
            403
        );
    }
}
