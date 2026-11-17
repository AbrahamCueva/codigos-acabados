<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\Dashboard\EntregasPorPeriodoWidget;
use App\Models\UserDashboardWidget;
use App\Filament\Widgets\Dashboard\TotalCodigosWidget;
use App\Filament\Widgets\Dashboard\StockDisponibleWidget;
use App\Filament\Widgets\Dashboard\EntregasRealizadasWidget;
use App\Filament\Widgets\Dashboard\TrabajadoresActivosWidget;
use App\Filament\Widgets\Dashboard\StockBajoWidget;
use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\Dashboard\UltimasActividadesWidget;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Escritorio';

    public function getColumns(): int|array
    {
        return [
            'default' => 1,
            'sm' => 2,
            'xl' => 4,
        ];
    }

    public function getWidgets(): array
    {
        $usuario = auth()->user();

        if (!$usuario) {
            return [];
        }

        $widgets = [
            'total_codigos' => [
                'class' => TotalCodigosWidget::class,
                'permission' => 'codigos.viewAny',
            ],

            'stock_disponible' => [
                'class' => StockDisponibleWidget::class,
                'permission' => 'codigos.viewAny',
            ],

            'entregas_realizadas' => [
                'class' => EntregasRealizadasWidget::class,
                'permission' => 'entregas.viewAny',
            ],

            'trabajadores_activos' => [
                'class' => TrabajadoresActivosWidget::class,
                'permission' => 'trabajadores.viewAny',
            ],

            'grafico_entregas' => [
                'class' => EntregasPorPeriodoWidget::class,
                'permission' => 'entregas.viewAny',
            ],

            'stock_bajo' => [
                'class' => StockBajoWidget::class,
                'permission' => 'codigos.viewAny',
            ],

            'ultimas_actividades' => [
                'class' => UltimasActividadesWidget::class,
                'permission' => 'actividades.viewAny',
            ],
        ];

        // El administrador siempre ve todos los widgets registrados.
        $esAdministrador = $usuario->hasRole('administrador');

        $preferencias = $esAdministrador
            ? collect()
            : UserDashboardWidget::query()
                ->where('user_id', $usuario->id)
                ->pluck('is_visible', 'widget_key');

        $resultado = [];

        foreach ($widgets as $key => $widget) {
            $tienePermiso = $esAdministrador
                || $usuario->can($widget['permission']);

            $estaVisible = $esAdministrador
                || (bool) $preferencias->get($key, true);

            if ($tienePermiso && $estaVisible) {
                $resultado[] = $widget['class'];
            }
        }

        return $resultado;
    }
}
