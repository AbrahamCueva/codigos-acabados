<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\Codigo;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Collection;

class StockBajoWidget extends Widget
{
    protected string $view = 'filament.widgets.dashboard.stock-bajo-widget';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $usuario = auth()->user();

        return (bool) (
            $usuario?->hasRole('administrador')
            || $usuario?->can('codigos.viewAny')
        );
    }

    public function getCodigos(): Collection
    {
        return Codigo::query()
            ->where('stock', '<=', 2)
            ->orderBy('stock')
            ->orderBy('codigo')
            ->limit(10)
            ->get(['id', 'codigo', 'stock']);
    }

    public function getTotalAlertas(): int
    {
        return Codigo::query()
            ->where('stock', '<=', 2)
            ->count();
    }

    public function getTotalCriticos(): int
    {
        return Codigo::query()
            ->where('stock', 0)
            ->count();
    }
}
