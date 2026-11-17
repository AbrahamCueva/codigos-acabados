<x-filament-widgets::widget>
    @php
        $actividades = $this->getActividades();


        $modulos = [
            'configuracion' => [
                'nombre' => 'Configuración',
                'color' => '#a78bfa',
                'fondo' => 'rgba(167, 139, 250, .12)',
                'icono' => 'heroicon-o-cog-6-tooth',
            ],
            'codigos' => [
                'nombre' => 'Códigos',
                'color' => '#38bdf8',
                'fondo' => 'rgba(56, 189, 248, .12)',
                'icono' => 'heroicon-o-qr-code',
            ],
            'entregas' => [
                'nombre' => 'Entregas',
                'color' => '#34d399',
                'fondo' => 'rgba(52, 211, 153, .12)',
                'icono' => 'heroicon-o-truck',
            ],
            'trabajadores' => [
                'nombre' => 'Trabajadores',
                'color' => '#fbbf24',
                'fondo' => 'rgba(251, 191, 36, .12)',
                'icono' => 'heroicon-o-users',
            ],
            'usuarios' => [
                'nombre' => 'Usuarios',
                'color' => '#f472b6',
                'fondo' => 'rgba(244, 114, 182, .12)',
                'icono' => 'heroicon-o-user',
            ],
            'roles' => [
                'nombre' => 'Roles',
                'color' => '#fb923c',
                'fondo' => 'rgba(251, 146, 60, .12)',
                'icono' => 'heroicon-o-shield-check',
            ],
        ];

    @endphp

    <style>
        .activity-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 14px;
            margin-bottom: 20px;
        }

        .activity-heading {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .activity-heading-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            border-radius: 12px;
            color: #a78bfa;
            background: rgba(167, 139, 250, .12);
        }

        .activity-heading-title {
            font-size: 16px;
            font-weight: 700;
        }

        .activity-heading-description {
            margin-top: 3px;
            color: #9ca3af;
            font-size: 12px;
        }

        .activity-count {
            padding: 7px 11px;
            border: 1px solid rgba(167, 139, 250, .2);
            border-radius: 8px;
            background: rgba(167, 139, 250, .08);
            color: #c4b5fd;
            font-size: 11px;
            font-weight: 700;
        }

        .activity-list {
            display: flex;
            flex-direction: column;
        }

        .activity-item {
            display: flex;
            align-items: flex-start;
            gap: 13px;
            padding: 15px 2px;
            border-bottom: 1px solid rgba(156, 163, 175, .12);
        }

        .activity-item:last-child {
            border-bottom: 0;
        }

        .activity-module-icon {
            display: flex;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 11px;
        }

        .activity-content {
            min-width: 0;
            flex: 1;
        }

        .activity-description {
            font-size: 13px;
            font-weight: 600;
            line-height: 1.5;
            overflow-wrap: anywhere;
        }

        .activity-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 7px;
            margin-top: 6px;
            color: #9ca3af;
            font-size: 11px;
        }

        .activity-dot {
            width: 3px;
            height: 3px;
            border-radius: 50%;
            background: #6b7280;
        }

        .activity-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 35px 15px;
            border: 1px dashed rgba(156, 163, 175, .2);
            border-radius: 12px;
            text-align: center;
        }

        .activity-empty-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            margin-bottom: 12px;
            border-radius: 50%;
            background: rgba(156, 163, 175, .1);
            color: #9ca3af;
        }

        .activity-empty-title {
            font-size: 13px;
            font-weight: 700;
        }

        .activity-empty-text {
            margin-top: 5px;
            color: #9ca3af;
            font-size: 11px;
        }

        .activity-footer {
            display: flex;
            justify-content: flex-end;
            padding-top: 14px;
            border-top: 1px solid rgba(156, 163, 175, .12);
        }

        .activity-link {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: #a78bfa;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
        }

        .activity-link:hover {
            color: #c4b5fd;
        }
    </style>

    <x-filament::section>
        <div>
            <div class="activity-header">
                <div class="activity-heading">
                    <div class="activity-heading-icon">
                        <x-filament::icon icon="heroicon-o-clock" style="width: 23px; height: 23px;" />
                    </div>

                    <div>
                        <div class="activity-heading-title">
                            Últimas actividades
                        </div>

                        <div class="activity-heading-description">
                            Movimientos recientes registrados en el sistema.
                        </div>
                    </div>
                </div>

                <span class="activity-count">
                    5 recientes
                </span>
            </div>

            @if ($actividades->isNotEmpty())
                <div class="activity-list">
                    @foreach ($actividades as $actividad)
                        @php
                            $modulo = $modulos[$actividad->log_name] ?? [
                                'color' => '#9ca3af',
                                'fondo' => 'rgba(156, 163, 175, .12)',
                                'icono' => 'heroicon-o-document-text',
                            ];

                            $usuario = $actividad->causer?->name ?? 'Sistema';
                        @endphp

                        <div class="activity-item" wire:key="actividad-dashboard-{{ $actividad->id }}">
                            <div class="activity-module-icon"
                                style="color: {{ $modulo['color'] }}; background: {{ $modulo['fondo'] }};">
                                <x-filament::icon :icon="$modulo['icono']" style="width: 19px; height: 19px;" />
                            </div>

                            <div class="activity-content">
                                <div class="activity-description">
                                    {{ $actividad->description }}
                                </div>

                                <div class="activity-meta">
                                    <span style="color: var(--gray-500); font-weight: 600;">
                                        {{ $usuario }}
                                    </span>

                                    <span class="activity-dot"></span>

                                    <span style="color: {{ $modulo['color'] }}; font-weight: 600;">
                                        {{ $modulo['nombre'] ?? ucfirst($actividad->log_name ?? 'General') }}
                                    </span>

                                    <span class="activity-dot"></span>

                                    <span>
                                        {{ $actividad->created_at?->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="activity-empty">
                    <div class="activity-empty-icon">
                        <x-filament::icon icon="heroicon-o-clipboard-document-list" style="width: 25px; height: 25px;" />
                    </div>

                    <div class="activity-empty-title">
                        Sin movimientos registrados
                    </div>

                    <p class="activity-empty-text">
                        Las actividades del sistema aparecerán aquí.
                    </p>
                </div>
            @endif

            <div class="activity-footer">
                <a href="{{ url('/admin/actividades/actividads') }}" class="activity-link">
                    Ver historial completo

                    <x-filament::icon icon="heroicon-m-arrow-right" style="width: 15px; height: 15px;" />
                </a>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>