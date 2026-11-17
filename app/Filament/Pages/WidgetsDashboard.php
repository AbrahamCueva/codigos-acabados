<?php

namespace App\Filament\Pages;

use App\Models\User;
use App\Models\UserDashboardWidget;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;
use UnitEnum;

class WidgetsDashboard extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static string|UnitEnum|null $navigationGroup = 'Administración';

    protected static ?string $navigationLabel = 'Widgets del Dashboard';

    protected static ?int $navigationSort = 20;

    protected static ?string $title = 'Widgets del Dashboard';

    protected string $view = 'filament.pages.widgets-dashboard';

    public ?string $selectedUserId = null;

    public array $selectedWidgets = [];

    private const WIDGETS = [
        'total_codigos' => [
            'nombre' => 'Códigos registrados',
            'descripcion' => 'Cantidad total de códigos registrados en el sistema.',
            'permiso' => 'codigos.viewAny',
        ],

        'stock_disponible' => [
            'nombre' => 'Stock disponible',
            'descripcion' => 'Cantidad total de rollos disponibles.',
            'permiso' => 'codigos.viewAny',
        ],

        'entregas_realizadas' => [
            'nombre' => 'Entregas realizadas',
            'descripcion' => 'Resumen de las entregas registradas.',
            'permiso' => 'entregas.viewAny',
        ],

        'trabajadores_activos' => [
            'nombre' => 'Trabajadores activos',
            'descripcion' => 'Cantidad de trabajadores habilitados.',
            'permiso' => 'trabajadores.viewAny',
        ],

        'grafico_entregas' => [
            'nombre' => 'Gráfico de entregas',
            'descripcion' => 'Evolución de las entregas por período.',
            'permiso' => 'entregas.viewAny',
        ],

        'stock_bajo' => [
            'nombre' => 'Alertas de stock bajo',
            'descripcion' => 'Códigos cuyo stock necesita atención.',
            'permiso' => 'codigos.viewAny',
        ],

        'ultimas_actividades' => [
            'nombre' => 'Últimas actividades',
            'descripcion' => 'Movimientos recientes registrados en el sistema.',
            'permiso' => 'actividades.viewAny',
        ],
    ];

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('administrador') ?? false;
    }

    public function users()
    {
        return User::query()
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'administrador');
            })
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
    }

    public function widgets(): array
    {
        return self::WIDGETS;
    }

    public function updatedSelectedUserId(): void
    {
        $this->selectedWidgets = [];

        if (! $this->selectedUserId) {
            return;
        }

        $usuario = $this->obtenerUsuarioSeleccionado();

        $preferencias = UserDashboardWidget::query()
            ->where('user_id', $usuario->id)
            ->pluck('is_visible', 'widget_key');

        foreach (self::WIDGETS as $key => $widget) {
            // No mostrar como seleccionados los widgets sin permiso.
            if (! $usuario->can($widget['permiso'])) {
                continue;
            }

            if ($preferencias->get($key, true)) {
                $this->selectedWidgets[] = $key;
            }
        }
    }

    public function save(): void
    {
        $usuario = $this->obtenerUsuarioSeleccionado();

        // Obtener primero los permisos efectivos del usuario.
        $permisos = [];

        foreach (self::WIDGETS as $key => $widget) {
            $permisos[$key] = $usuario->can($widget['permiso']);
        }

        // Guardar únicamente widgets autorizados y seleccionados.
        $seleccionados = array_values(array_filter(
            array_intersect(
                array_keys(self::WIDGETS),
                $this->selectedWidgets
            ),
            fn (string $key): bool => $permisos[$key] ?? false
        ));

        $preferenciasActuales = UserDashboardWidget::query()
            ->where('user_id', $usuario->id)
            ->pluck('is_visible', 'widget_key');

        $cambios = [];

        DB::transaction(function () use (
            $usuario,
            $seleccionados,
            $preferenciasActuales,
            $permisos,
            &$cambios
        ): void {
            foreach (self::WIDGETS as $key => $widget) {
                // No modificar preferencias de widgets sin permiso.
                if (! ($permisos[$key] ?? false)) {
                    continue;
                }

                $anterior = (bool) $preferenciasActuales->get($key, true);
                $nuevo = in_array($key, $seleccionados, true);

                if ($anterior === $nuevo) {
                    continue;
                }

                UserDashboardWidget::updateOrCreate(
                    [
                        'user_id' => $usuario->id,
                        'widget_key' => $key,
                    ],
                    [
                        'is_visible' => $nuevo,
                    ]
                );

                $cambios[$key] = [
                    'campo' => $widget['nombre'],
                    'antes' => $anterior ? 'Visible' : 'Oculto',
                    'despues' => $nuevo ? 'Visible' : 'Oculto',
                ];
            }

            // Registrar los cambios en el historial.
            if ($cambios !== []) {
                activity('dashboard_widgets')
                    ->causedBy(auth()->user())
                    ->performedOn($usuario)
                    ->withProperties([
                        'cambios' => $cambios,
                        'usuario_configurado' => $usuario->name,
                    ])
                    ->log(
                        'Modificó los widgets del Dashboard de ' . $usuario->name
                    );
            }
        });

        Notification::make()
            ->title('Preferencias actualizadas')
            ->body(
                $cambios === []
                    ? 'No se detectaron cambios en los widgets autorizados.'
                    : 'Se actualizaron las preferencias de ' . $usuario->name . '.'
            )
            ->success()
            ->send();

        $this->updatedSelectedUserId();
    }

    private function obtenerUsuarioSeleccionado(): User
    {
        abort_if(
            ! $this->selectedUserId,
            422,
            'Selecciona un usuario.'
        );

        return User::query()
            ->whereKey($this->selectedUserId)
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'administrador');
            })
            ->firstOrFail();
    }

    public function widgetPermissions(): array
    {
        if (! $this->selectedUserId) {
            return [];
        }

        $usuario = $this->obtenerUsuarioSeleccionado();

        $resultado = [];

        foreach (self::WIDGETS as $key => $widget) {
            $resultado[$key] = $usuario->can($widget['permiso']);
        }

        return $resultado;
    }
}
