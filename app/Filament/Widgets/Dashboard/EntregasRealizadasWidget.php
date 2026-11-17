<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\Entrega;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EntregasRealizadasWidget extends StatsOverviewWidget
{
    protected int|string|array $columnSpan = 1;

    public static function canView(): bool
    {
        $usuario = auth()->user();

        return $usuario?->hasRole('administrador')
            || $usuario?->can('entregas.viewAny');
    }

    protected function getStats(): array
    {
        return [
            Stat::make(
                'Entregas realizadas',
                Entrega::query()->count()
            )
                ->description('Total de entregas registradas')
                ->descriptionIcon('heroicon-m-truck')
                ->color('info'),
        ];
    }
}
