<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\Trabajador;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TrabajadoresActivosWidget extends StatsOverviewWidget
{
    protected int|string|array $columnSpan = 1;

    public static function canView(): bool
    {
        $usuario = auth()->user();

        return $usuario?->hasRole('administrador')
            || $usuario?->can('trabajadores.viewAny');
    }

    protected function getStats(): array
    {
        return [
            Stat::make(
                'Trabajadores activos',
                Trabajador::query()->where('activo', true)->count()
            )
                ->description('Trabajadores habilitados')
                ->descriptionIcon('heroicon-m-users')
                ->color('warning'),
        ];
    }
}
