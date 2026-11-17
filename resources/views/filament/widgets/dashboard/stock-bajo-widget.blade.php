
<x-filament-widgets::widget>
    @php
        $codigos = $this->getCodigos();
        $totalAlertas = $this->getTotalAlertas();
        $totalCriticos = $this->getTotalCriticos();
        $totalBajos = $totalAlertas - $totalCriticos;
    @endphp

    <style>
        .stock-widget {
            color: var(--gray-950);
        }

        .dark .stock-widget {
            color: white;
        }

        .stock-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 22px;
        }

        .stock-title-group {
            display: flex;
            align-items: center;
            gap: 13px;
        }

        .stock-main-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 46px;
            height: 46px;
            border-radius: 13px;
            color: #f59e0b;
            background: rgba(245, 158, 11, .12);
        }

        .stock-title {
            font-size: 16px;
            font-weight: 700;
        }

        .stock-subtitle {
            margin-top: 4px;
            color: #9ca3af;
            font-size: 12px;
        }

        .stock-indicators {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 22px;
        }

        .stock-indicator {
            padding: 15px;
            border: 1px solid rgba(156, 163, 175, .16);
            border-radius: 12px;
            background: rgba(156, 163, 175, .045);
        }

        .stock-indicator-label {
            display: flex;
            align-items: center;
            gap: 7px;
            color: #9ca3af;
            font-size: 12px;
        }

        .stock-indicator-value {
            margin-top: 7px;
            font-size: 25px;
            font-weight: 750;
            line-height: 1;
        }

        .stock-warning { color: #f59e0b; }
        .stock-danger { color: #ef4444; }
        .stock-success { color: #10b981; }

        .stock-table-wrapper {
            overflow-x: auto;
            border: 1px solid rgba(156, 163, 175, .15);
            border-radius: 12px;
        }

        .stock-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 13px;
        }

        .stock-table th {
            padding: 13px 16px;
            color: #9ca3af;
            background: rgba(156, 163, 175, .06);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .stock-table td {
            padding: 15px 16px;
            border-top: 1px solid rgba(156, 163, 175, .12);
        }

        .stock-table tbody tr:hover {
            background: rgba(156, 163, 175, .045);
        }

        .stock-code {
            font-weight: 700;
        }

        .stock-quantity {
            font-weight: 750;
            font-size: 15px;
        }

        .stock-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 7px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        .stock-badge.warning {
            color: #fbbf24;
            background: rgba(245, 158, 11, .12);
        }

        .stock-badge.danger {
            color: #f87171;
            background: rgba(239, 68, 68, .12);
        }

        .stock-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 38px 20px;
            border: 1px dashed rgba(16, 185, 129, .3);
            border-radius: 12px;
            background: rgba(16, 185, 129, .035);
            text-align: center;
        }

        .stock-empty-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 52px;
            height: 52px;
            margin-bottom: 13px;
            border-radius: 50%;
            color: #10b981;
            background: rgba(16, 185, 129, .12);
        }

        .stock-empty-title {
            font-size: 14px;
            font-weight: 700;
        }

        .stock-empty-text {
            margin-top: 5px;
            color: #9ca3af;
            font-size: 12px;
        }

        .stock-footer {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 14px;
            color: #9ca3af;
            font-size: 11px;
        }

        @media (max-width: 640px) {
            .stock-indicators {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <x-filament::section>
        <div class="stock-widget">

            <div class="stock-header">
                <div class="stock-title-group">
                    <div class="stock-main-icon">
                        <x-filament::icon
                            icon="heroicon-o-exclamation-triangle"
                            style="width: 23px; height: 23px;"
                        />
                    </div>

                    <div>
                        <div class="stock-title">Alertas de stock bajo</div>
                        <div class="stock-subtitle">
                            Seguimiento de códigos con 2 rollos o menos disponibles.
                        </div>
                    </div>
                </div>

                <span style="font-size: 11px; color: #9ca3af;">
                    Actualizado al consultar el dashboard
                </span>
            </div>

            <div class="stock-indicators">
                <div class="stock-indicator">
                    <div class="stock-indicator-label">
                        <span class="stock-warning">●</span>
                        Total de alertas
                    </div>
                    <div class="stock-indicator-value stock-warning">
                        {{ $totalAlertas }}
                    </div>
                </div>

                <div class="stock-indicator">
                    <div class="stock-indicator-label">
                        <span class="stock-danger">●</span>
                        Stock crítico
                    </div>
                    <div class="stock-indicator-value stock-danger">
                        {{ $totalCriticos }}
                    </div>
                </div>

                <div class="stock-indicator">
                    <div class="stock-indicator-label">
                        <span class="stock-success">●</span>
                        Stock bajo
                    </div>
                    <div class="stock-indicator-value stock-success">
                        {{ $totalBajos }}
                    </div>
                </div>
            </div>

            @if ($codigos->isNotEmpty())

                <div class="stock-table-wrapper">
                    <table class="stock-table">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Stock disponible</th>
                                <th>Estado</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($codigos as $codigo)
                                <tr wire:key="stock-bajo-{{ $codigo->id }}">
                                    <td>
                                        <span class="stock-code">
                                            {{ $codigo->codigo }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="stock-quantity {{ $codigo->stock == 0 ? 'stock-danger' : 'stock-warning' }}">
                                            {{ number_format($codigo->stock) }}
                                        </span>
                                        <span style="color: #9ca3af; font-size: 11px;">
                                            rollos
                                        </span>
                                    </td>

                                    <td>
                                        @if ((int) $codigo->stock === 0)
                                            <span class="stock-badge danger">
                                                <x-filament::icon
                                                    icon="heroicon-m-x-circle"
                                                    style="width: 14px; height: 14px;"
                                                />
                                                Stock crítico
                                            </span>
                                        @else
                                            <span class="stock-badge warning">
                                                <x-filament::icon
                                                    icon="heroicon-m-exclamation-circle"
                                                    style="width: 14px; height: 14px;"
                                                />
                                                Stock bajo
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($totalAlertas > 10)
                    <div class="stock-footer">
                        <span>Mostrando 10 de {{ $totalAlertas }} códigos.</span>
                        <span>Existen {{ $totalAlertas - 10 }} alertas adicionales.</span>
                    </div>
                @endif

            @else

                <div class="stock-empty">
                    <div class="stock-empty-icon">
                        <x-filament::icon
                            icon="heroicon-o-check-circle"
                            style="width: 29px; height: 29px;"
                        />
                    </div>

                    <div class="stock-empty-title">
                        ¡Todo está en orden!
                    </div>

                    <p class="stock-empty-text">
                        No hay códigos con 2 rollos o menos disponibles.
                        El inventario se encuentra en buen estado.
                    </p>
                </div>

            @endif

            <div class="stock-footer">
                <span>Umbral de alerta: 2 rollos o menos</span>
                <span>Control de inventario · Códigos Acabados</span>
            </div>

        </div>
    </x-filament::section>
</x-filament-widgets::widget>
