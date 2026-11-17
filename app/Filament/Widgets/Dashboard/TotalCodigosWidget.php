<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\Codigo;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalCodigosWidget extends StatsOverviewWidget
{
    protected int|string|array $columnSpan = 1;

    public static function canView(): bool
    {
        $usuario = auth()->user();

        return $usuario?->hasRole('administrador')
            || $usuario?->can('codigos.viewAny');
    }

    protected function getStats(): array
    {
        return [
            Stat::make(
                'Códigos registrados',
                Codigo::query()->count()
            )
                ->description('Total de códigos registrados')
                ->descriptionIcon('heroicon-m-qr-code')
                ->color('primary'),
        ];
    }
}
