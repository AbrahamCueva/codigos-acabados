<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\Codigo;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StockDisponibleWidget extends StatsOverviewWidget
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
                'Stock disponible',
                number_format(
                    (int) Codigo::query()->sum('stock')
                )
            )
                ->description('Rollos disponibles en inventario')
                ->descriptionIcon('heroicon-m-cube')
                ->color('success'),
        ];
    }
}
