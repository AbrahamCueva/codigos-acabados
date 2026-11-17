<?php

namespace App\Filament\Resources\Actividades\Tables;

use App\Models\Codigo;
use App\Models\Trabajador;
use Carbon\Carbon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;

class ActividadesTable
{
    protected const ETIQUETAS = [
        'name' => 'Nombre',
        'email' => 'Correo electrónico',
        'nombre' => 'Nombre',
        'tipo' => 'Tipo',
        'activo' => 'Activo',
        'codigo' => 'Código',
        'stock' => 'Stock',
        'fecha' => 'Fecha',
        'codigo_id' => 'Código',
        'trabajador_id' => 'Trabajador',
        'rollos_entregados' => 'Rollos entregados',
        'rollos_restantes' => 'Rollos restantes',
    ];

    protected const PERMISOS = [
        'codigos.viewAny' => 'Ver códigos',
        'codigos.create' => 'Registrar códigos',
        'codigos.update' => 'Editar códigos',
        'codigos.update_stock' => 'Modificar stock',
        'codigos.delete' => 'Eliminar códigos',

        'entregas.viewAny' => 'Ver entregas',
        'entregas.create' => 'Registrar entregas',
        'entregas.update' => 'Editar entregas',
        'entregas.delete' => 'Eliminar entregas',

        'trabajadores.viewAny' => 'Ver trabajadores',
        'trabajadores.create' => 'Registrar trabajadores',
        'trabajadores.update' => 'Editar trabajadores',
        'trabajadores.delete' => 'Eliminar trabajadores',

        'actividades.viewAny' => 'Ver historial de actividades',
    ];

    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')


            ->columns([
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->width('150px')
                    ->toggleable(),

                TextColumn::make('causer.name')
                    ->label('Usuario')
                    ->placeholder('Sistema')
                    ->searchable()
                    ->width('150px'),

                TextColumn::make('log_name')
                    ->label('Módulo')
                    ->badge()
                    ->formatStateUsing(
                        fn(?string $state): string => match ($state) {
                            'codigos' => 'Códigos',
                            'entregas' => 'Entregas',
                            'trabajadores' => 'Trabajadores',
                            'usuarios' => 'Usuarios',
                            'roles' => 'Roles',
                            'configuracion' => 'Configuración',
                            'dashboard_widgets' => 'Dashboard',
                            default => ucfirst((string) $state),
                        }
                    )
                    ->width('130px'),

                TextColumn::make('description')
                    ->label('Actividad')
                    ->limit(65)
                    ->tooltip(fn(Activity $record): string => $record->description)
                    ->width('260px'),

                TextColumn::make('cambios')
                    ->label('Modificaciones')
                    ->state(fn(Activity $record): string => self::resumenCambios($record))
                    ->badge()
                    ->color('info')
                    ->tooltip(fn(Activity $record): string => implode(
                        "\n",
                        self::cambios($record)
                    ))
                    ->placeholder('Sin cambios'),
            ])


            ->filters([
                SelectFilter::make('log_name')
                    ->label('Módulo')
                    ->options([
                        'entregas' => 'Entregas',
                        'codigos' => 'Códigos',
                        'trabajadores' => 'Trabajadores',
                        'usuarios' => 'Usuarios',
                        'roles' => 'Roles',
                        'configuracion' => 'Configuración',
                    ]),

                SelectFilter::make('event')
                    ->label('Acción')
                    ->options([
                        'created' => 'Creado',
                        'updated' => 'Modificado',
                        'deleted' => 'Eliminado',
                    ]),
            ]);
    }

    /**
     * Obtiene los cambios registrados en una actividad.
     *
     * @return array<int, string>
     */
    protected static function cambios(Activity $registro): array
    {
        $propiedades = $registro->attribute_changes?->toArray() ?? [];

        $nuevos = (array) ($propiedades['attributes'] ?? []);
        $anteriores = (array) ($propiedades['old'] ?? []);

        $resultado = [];

        // Eliminaciones.
        if ($nuevos === [] && $anteriores !== []) {
            $resultado = collect($anteriores)
                ->map(
                    fn(mixed $valor, string $campo): string =>
                        (self::ETIQUETAS[$campo] ?? $campo)
                        . ': '
                        . self::formatearCampo(
                            $registro,
                            $campo,
                            $valor,
                            'old'
                        )
                )
                ->values()
                ->all();
        } else {
            // Creaciones y modificaciones.
            $resultado = collect($nuevos)
                ->map(function (mixed $valor, string $campo) use ($registro, $anteriores): string {
                    $etiqueta = self::ETIQUETAS[$campo] ?? $campo;

                    if (array_key_exists($campo, $anteriores)) {
                        return $etiqueta . ': '
                            . self::formatearCampo(
                                $registro,
                                $campo,
                                $anteriores[$campo],
                                'old'
                            )
                            . ' → '
                            . self::formatearCampo(
                                $registro,
                                $campo,
                                $valor,
                                'attributes'
                            );
                    }

                    return $etiqueta . ': '
                        . self::formatearCampo(
                            $registro,
                            $campo,
                            $valor,
                            'attributes'
                        );
                })
                ->values()
                ->all();
        }

        $snapshot = $registro->properties?->get('snapshot', []) ?? [];

        // Cambios de roles asignados a usuarios.
        $roles = $snapshot['roles'] ?? null;

        if ($roles !== null) {
            $rolesAnteriores = (array) ($roles['old'] ?? []);
            $rolesNuevos = (array) ($roles['attributes'] ?? []);

            if ($rolesAnteriores !== $rolesNuevos) {
                if ($rolesAnteriores === []) {
                    $resultado[] = 'Rol asignado: '
                        . self::formatearRoles($rolesNuevos);
                } elseif ($rolesNuevos === []) {
                    $resultado[] = 'Rol retirado: '
                        . self::formatearRoles($rolesAnteriores);
                } else {
                    $resultado[] = 'Rol: '
                        . self::formatearRoles($rolesAnteriores)
                        . ' → '
                        . self::formatearRoles($rolesNuevos);
                }
            }
        }

        // Cambios en los permisos de un rol.
        $permisos = $snapshot['permissions'] ?? null;

        if ($permisos !== null) {
            $agregados = (array) ($permisos['added'] ?? []);
            $retirados = (array) ($permisos['removed'] ?? []);

            if ($agregados !== []) {
                $resultado[] = 'Permisos agregados: '
                    . self::formatearPermisos($agregados);
            }

            if ($retirados !== []) {
                $resultado[] = 'Permisos retirados: '
                    . self::formatearPermisos($retirados);
            }

            // Compatibilidad con registros anteriores.
            if (
                $agregados === []
                && $retirados === []
                && isset($permisos['attributes'])
            ) {
                $anteriores = (array) ($permisos['old'] ?? []);
                $actuales = (array) ($permisos['attributes'] ?? []);

                if ($anteriores === [] && $actuales !== []) {
                    $resultado[] = 'Permisos asignados: '
                        . self::formatearPermisos($actuales);
                }
            }
        }

        // Información del rol eliminado.
        if ($registro->description === 'Rol eliminado') {
            $resultado[] = 'Rol eliminado: '
                . ($snapshot['name'] ?? 'Sin nombre');

            $resultado[] = 'Permisos que tenía: '
                . self::formatearPermisos(
                    (array) ($snapshot['permissions'] ?? [])
                );
        }


        // Cambios de configuración del sistema.
        $cambiosConfiguracion = $registro->properties?->get('cambios', []) ?? [];

        if (is_array($cambiosConfiguracion)) {
            foreach ($cambiosConfiguracion as $cambio) {
                if (!is_array($cambio)) {
                    continue;
                }

                $campo = $cambio['campo'] ?? 'Configuración';

                $anterior = $cambio['antes'] ?? null;
                $nuevo = $cambio['despues'] ?? null;

                // Evitar mostrar rutas completas de almacenamiento.
                if (
                    in_array($campo, [
                        'Logotipo principal',
                        'Logotipo para modo oscuro',
                        'Favicon',
                    ], true)
                ) {
                    $anterior = $anterior
                        ? 'Imagen anterior'
                        : 'Sin imagen';

                    $nuevo = $nuevo
                        ? 'Imagen actualizada'
                        : 'Sin imagen';
                }

                $resultado[] = "{$campo}: {$anterior} → {$nuevo}";
            }
        }

        return $resultado;
    }


    protected static function resumenCambios(Activity $registro): string
    {
        $cambios = self::cambios($registro);

        $cantidad = count($cambios);

        if ($cantidad === 0) {
            return 'Sin cambios';
        }

        return $cantidad === 1
            ? '1 modificación'
            : "{$cantidad} modificaciones";
    }


    /**
     * Formatea una lista de roles.
     */
    protected static function formatearRoles(array $roles): string
    {
        return $roles !== []
            ? implode(', ', $roles)
            : 'Sin rol';
    }

    /**
     * Traduce los permisos a nombres amigables.
     */
    protected static function formatearPermisos(array $permisos): string
    {
        return collect($permisos)
            ->map(
                fn(mixed $permiso): string =>
                    self::PERMISOS[(string) $permiso] ?? (string) $permiso
            )
            ->implode(', ');
    }

    /**
     * Formatea valores para su presentación.
     */
    protected static function formatear(mixed $valor): string
    {
        return match (true) {
            is_null($valor) => '—',

            is_bool($valor) => $valor ? 'Sí' : 'No',

            is_string($valor)
            && preg_match('/^\d{4}-\d{2}-\d{2}T/', $valor) === 1
            => Carbon::parse($valor)->format('d/m/Y'),

            is_array($valor) => json_encode(
                $valor,
                JSON_UNESCAPED_UNICODE
            ) ?: '—',

            default => (string) $valor,
        };
    }

    /**
     * Formatea campos relacionados utilizando sus snapshots.
     */
    protected static function formatearCampo(
        Activity $registro,
        string $campo,
        mixed $valor,
        string $lado
    ): string {
        if (is_null($valor)) {
            return '—';
        }

        $snapshot = $registro->properties?->get('snapshot', []) ?? [];

        return match ($campo) {
            'codigo_id' => $snapshot[$lado]['codigo']
                ?? Codigo::find($valor)?->codigo
                ?? "Código eliminado (ID: {$valor})",

            'trabajador_id' => $snapshot[$lado]['trabajador']
                ?? Trabajador::find($valor)?->nombre
                ?? "Trabajador eliminado (ID: {$valor})",

            default => self::formatear($valor),
        };
    }
}
