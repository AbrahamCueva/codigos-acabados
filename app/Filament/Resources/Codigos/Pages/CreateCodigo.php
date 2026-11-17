<?php

namespace App\Filament\Resources\Codigos\Pages;

use App\Filament\Resources\Codigos\CodigoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCodigo extends CreateRecord
{
    protected static string $resource = CodigoResource::class;

    protected function beforeCreate(): void
    {
        abort_unless(
            auth()->user()?->can('codigos.create'),
            403
        );
    }
}
