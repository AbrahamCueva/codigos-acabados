<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\Entrega;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class EntregasPorPeriodoWidget extends ChartWidget
{
    protected ?string $heading = 'Evolución de entregas';

    protected ?string $description = 'Cantidad de entregas registradas por período.';

    protected ?string $maxHeight = '260px';

    protected int|string|array $columnSpan = 'full';

    public ?string $filter = '7d';

    public static function canView(): bool
    {
        $usuario = auth()->user();

        return (bool) (
            $usuario?->hasRole('administrador')
            || $usuario?->can('entregas.viewAny')
        );
    }

    protected function getFilters(): ?array
    {
        return [
            '7d' => 'Últimos 7 días',
            '30d' => 'Últimos 30 días',
            '12m' => 'Últimos 12 meses',
        ];
    }

    protected function getData(): array
    {
        $filtro = $this->filter ?? '7d';

        if ($filtro === '12m') {
            $inicio = now()->subMonths(11)->startOfMonth();
            $fin = now()->endOfMonth();

            $resultados = Entrega::query()
                ->selectRaw("DATE_FORMAT(fecha, '%Y-%m') as periodo, COUNT(*) as total")
                ->whereBetween('fecha', [$inicio, $fin])
                ->groupByRaw("DATE_FORMAT(fecha, '%Y-%m')")
                ->orderBy('periodo')
                ->pluck('total', 'periodo');

            $labels = [];
            $datos = [];

            $fecha = $inicio->copy();

            while ($fecha <= $fin) {
                $periodo = $fecha->format('Y-m');

                $labels[] = $fecha->translatedFormat('M Y');
                $datos[] = (int) ($resultados[$periodo] ?? 0);

                $fecha->addMonth();
            }
        } else {
            $dias = $filtro === '30d' ? 29 : 6;

            $inicio = now()->subDays($dias)->startOfDay();
            $fin = now()->endOfDay();

            $resultados = Entrega::query()
                ->selectRaw('DATE(fecha) as periodo, COUNT(*) as total')
                ->whereBetween('fecha', [$inicio, $fin])
                ->groupByRaw('DATE(fecha)')
                ->orderBy('periodo')
                ->pluck('total', 'periodo');

            $labels = [];
            $datos = [];

            $fecha = $inicio->copy();

            while ($fecha->toDateString() <= $fin->toDateString()) {
                $periodo = $fecha->toDateString();

                $labels[] = $fecha->format('d/m');
                $datos[] = (int) ($resultados[$periodo] ?? 0);

                $fecha->addDay();
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Entregas',
                    'data' => $datos,
                    'borderColor' => '#F5A623',
                    'backgroundColor' => 'rgba(245, 166, 35, 0.12)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
