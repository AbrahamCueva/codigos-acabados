<?php

namespace App\Filament\Resources\Codigos\Pages;

use App\Filament\Resources\Codigos\CodigoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCodigos extends ListRecords
{
    protected static string $resource = CodigoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn(): bool => auth()->user()?->can('codigos.create') ?? false),
        ];
    }
}
