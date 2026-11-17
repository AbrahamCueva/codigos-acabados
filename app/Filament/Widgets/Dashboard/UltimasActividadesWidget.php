<?php

namespace App\Filament\Widgets\Dashboard;

use Filament\Widgets\Widget;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Database\Eloquent\Collection;

class UltimasActividadesWidget extends Widget
{
    protected string $view = 'filament.widgets.dashboard.ultimas-actividades-widget';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $usuario = auth()->user();

        return (bool) (
            $usuario?->hasRole('administrador')
            || $usuario?->can('actividades.viewAny')
        );
    }

    public function getActividades(): Collection
    {
        return Activity::query()
            ->with('causer')
            ->latest('created_at')
            ->limit(5)
            ->get();
    }
}
