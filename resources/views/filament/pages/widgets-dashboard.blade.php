<x-filament-panels::page>

    <div class="dw-container">

        {{-- Encabezado --}}

        <div class="dw-header">

            <div>

                <h2>Personalización del Dashboard</h2>

                <p>

                    Administra los componentes que cada usuario puede visualizar

                    en su escritorio.

                </p>

            </div>

            <div class="dw-header-icon">

                <x-filament::icon icon="heroicon-o-squares-2x2" />

            </div>

        </div>

        {{-- Selector de usuario --}}

        <div class="dw-panel">

            <div class="dw-panel-heading">

                <div class="dw-heading-icon">

                    <x-filament::icon icon="heroicon-o-user-circle" />

                </div>

                <div>

                    <h3>Seleccionar usuario</h3>

                    <p>Elige la cuenta que deseas personalizar.</p>

                </div>

            </div>

            <div class="dw-user-selector">

                <select wire:model.live="selectedUserId">

                    <option value="">Selecciona un usuario...</option>

                    @foreach ($this->users() as $user)

                        <option value="{{ $user->id }}">

                            {{ $user->name }} — {{ $user->email }}

                        </option>

                    @endforeach

                </select>

                <span class="dw-user-hint">

                    <x-filament::icon icon="heroicon-o-information-circle" />

                    Las preferencias se guardan individualmente.

                </span>

            </div>

        </div>

        @if ($selectedUserId)

            @php
                $permisosWidgets = $this->widgetPermissions();

                $widgetsAutorizados = collect($permisosWidgets)
                    ->filter(fn($permitido) => $permitido)
                    ->count();

                $widgetsActivos = collect($selectedWidgets)
                    ->filter(fn($key) => $permisosWidgets[$key] ?? false)
                    ->count();

                $iconos = [
                    'total_codigos' => 'heroicon-o-qr-code',
                    'stock_disponible' => 'heroicon-o-cube',
                    'entregas_realizadas' => 'heroicon-o-truck',
                    'trabajadores_activos' => 'heroicon-o-users',
                    'grafico_entregas' => 'heroicon-o-chart-bar',
                    'stock_bajo' => 'heroicon-o-exclamation-triangle',
                    'ultimas_actividades' => 'heroicon-o-clock',
                ];
            @endphp
            {{-- Widgets --}}

            <div class="dw-panel">

                <div class="dw-panel-heading dw-widgets-heading">

                    <div class="dw-heading-icon">

                        <x-filament::icon icon="heroicon-o-adjustments-horizontal" />

                    </div>

                    <div>

                        <h3>Widgets disponibles</h3>

                        <p>

                            Activa o desactiva los componentes del escritorio

                            de este usuario.

                        </p>

                    </div>

                    <div class="dw-counter">

                        <strong>{{ $widgetsActivos }}</strong>

                        <span>de {{ $widgetsAutorizados }} disponibles</span>

                    </div>

                </div>

                <div class="dw-grid">

                    @foreach ($this->widgets() as $key => $widget)

                        @php

                            $autorizado = $permisosWidgets[$key] ?? false;

                            $activo = in_array($key, $selectedWidgets, true);

                        @endphp

                        <label wire:key="widget-{{ $key }}"
                            class="dw-card {{ !$autorizado ? 'dw-card-locked' : '' }} {{ $activo && $autorizado ? 'dw-card-active' : '' }}">

                            <div class="dw-card-icon">

                                <x-filament::icon :icon="$iconos[$key] ?? 'heroicon-o-squares-2x2'" />

                            </div>

                            <div class="dw-card-content">

                                <strong>{{ $widget['nombre'] }}</strong>

                                <p>{{ $widget['descripcion'] }}</p>

                                @if ($autorizado)

                                    <span class="dw-permission dw-permission-ok">

                                        <x-filament::icon icon="heroicon-o-shield-check" />

                                        Permiso disponible

                                    </span>

                                @else

                                    <span class="dw-permission dw-permission-denied">

                                        <x-filament::icon icon="heroicon-o-lock-closed" />

                                        Requiere: {{ $widget['permiso'] }}

                                    </span>

                                @endif

                            </div>

                            <div class="dw-switch">

                                <input type="checkbox" wire:model="selectedWidgets" value="{{ $key }}"
                                    aria-label="Mostrar {{ $widget['nombre'] }}" @disabled(!$autorizado)>

                                <span></span>

                            </div>

                        </label>

                    @endforeach

                </div>

                <div class="dw-footer">

                    <span>

                        <x-filament::icon icon="heroicon-o-information-circle" />

                        Los permisos del sistema siempre tienen prioridad.

                    </span>

                    <x-filament::button wire:click="save" wire:loading.attr="disabled" icon="heroicon-o-check">

                        <span wire:loading.remove wire:target="save">

                            Guardar preferencias

                        </span>

                        <span wire:loading wire:target="save">

                            Guardando...

                        </span>

                    </x-filament::button>

                </div>

            </div>

        @else

            <div class="dw-empty">

                <div class="dw-empty-icon">

                    <x-filament::icon icon="heroicon-o-user-group" />

                </div>

                <h3>Selecciona un usuario</h3>

                <p>

                    Para comenzar, selecciona una cuenta y podrás

                    personalizar los widgets de su Dashboard.

                </p>

            </div>

        @endif

        {{-- Información --}}

        <div class="dw-security">

            <x-filament::icon icon="heroicon-o-shield-check" />

            <div>

                <strong>Control de acceso protegido</strong>

                <p>

                    Los administradores siempre conservan todos sus widgets.

                    Los demás usuarios solo visualizarán los componentes

                    habilitados y permitidos por sus roles.

                </p>

            </div>

        </div>

    </div>

    <style>
        .dw-container {

            display: flex;

            flex-direction: column;

            gap: 22px;

        }

        .dw-header {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 20px;

            padding: 8px 2px;

        }

        .dw-header h2 {

            font-size: 22px;

            font-weight: 750;

            letter-spacing: -.5px;

        }

        .dw-header p,

        .dw-panel-heading p {

            color: var(--gray-500);

            font-size: 13px;

            margin-top: 5px;

        }

        .dw-header-icon,

        .dw-heading-icon,

        .dw-card-icon,

        .dw-empty-icon {

            display: grid;

            place-items: center;

            flex-shrink: 0;

            border-radius: 12px;

            background: color-mix(in srgb, var(--primary-500) 12%, transparent);

            color: var(--primary-400);

        }

        .dw-header-icon {

            width: 52px;

            height: 52px;

            font-size: 25px;

        }

        .dw-panel {

            overflow: hidden;

            border: 1px solid var(--gray-800);

            border-radius: 14px;

            background: var(--gray-950);

        }

        .dw-panel-heading {

            display: flex;

            align-items: center;

            gap: 13px;

            padding: 20px;

            border-bottom: 1px solid var(--gray-800);

        }

        .dw-heading-icon {

            width: 40px;

            height: 40px;

            font-size: 20px;

        }

        .dw-panel-heading h3 {

            font-size: 15px;

            font-weight: 700;

        }

        .dw-user-selector {

            padding: 20px;

        }

        .dw-user-selector select {

            width: 100%;

            min-height: 46px;

            padding: 10px 14px;

            border: 1px solid var(--gray-700);

            border-radius: 9px;

            background: var(--gray-900);

            color: var(--gray-100);

            outline: none;

        }

        .dw-user-selector select:focus {

            border-color: var(--primary-500);

        }

        .dw-user-hint,

        .dw-footer>span {

            display: flex;

            align-items: center;

            gap: 7px;

            margin-top: 12px;

            color: var(--gray-500);

            font-size: 12px;

        }

        .dw-widgets-heading {

            flex-wrap: wrap;

        }

        .dw-counter {

            display: flex;

            align-items: center;

            gap: 8px;

            margin-left: auto;

            padding: 7px 12px;

            border-radius: 9px;

            background: color-mix(in srgb, var(--primary-500) 10%, transparent);

            color: var(--primary-400);

            font-size: 12px;

        }

        .dw-counter strong {

            font-size: 17px;

        }

        .dw-grid {

            display: grid;

            grid-template-columns: repeat(2, minmax(0, 1fr));

            gap: 14px;

            padding: 20px;

        }

        .dw-card {

            display: flex;

            align-items: flex-start;

            gap: 13px;

            min-width: 0;

            padding: 17px;

            border: 1px solid var(--gray-800);

            border-radius: 12px;

            background: var(--gray-900);

            cursor: pointer;

            transition: border-color .2s, background .2s;

        }

        .dw-card:hover {

            border-color: var(--primary-500);

        }

        .dw-card:has(.dw-switch input:checked) {

            border-color: color-mix(in srgb, var(--primary-500) 65%, transparent);

            background: color-mix(in srgb, var(--primary-500) 5%, var(--gray-900));

        }

        .dw-card-icon {

            width: 42px;

            height: 42px;

            font-size: 20px;

        }

        .dw-card-content {

            flex: 1;

            min-width: 0;

        }

        .dw-card-content strong {

            display: block;

            font-size: 13px;

            font-weight: 700;

        }

        .dw-card-content p {

            margin-top: 5px;

            color: var(--gray-400);

            font-size: 12px;

            line-height: 1.6;

        }

        .dw-permission {

            display: inline-flex;

            align-items: center;

            gap: 5px;

            margin-top: 12px;

            color: var(--gray-500);

            font-size: 10px;

        }

        .dw-switch {

            position: relative;

            width: 39px;

            height: 22px;

            flex-shrink: 0;

            margin-top: 2px;

        }

        .dw-switch input {

            position: absolute;

            width: 100%;

            height: 100%;

            opacity: 0;

            cursor: pointer;

            z-index: 2;

        }

        .dw-switch span {

            position: absolute;

            inset: 0;

            border-radius: 20px;

            background: var(--gray-700);

            transition: background .2s;

        }

        .dw-switch span::after {

            content: '';

            position: absolute;

            top: 3px;

            left: 3px;

            width: 16px;

            height: 16px;

            border-radius: 50%;

            background: white;

            transition: transform .2s;

        }

        .dw-switch input:checked+span {

            background: var(--primary-600);

        }

        .dw-switch input:checked+span::after {

            transform: translateX(17px);

        }

        .dw-switch input:focus-visible+span {

            outline: 2px solid var(--primary-400);

            outline-offset: 3px;

        }

        .dw-footer {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 15px;

            padding: 17px 20px;

            border-top: 1px solid var(--gray-800);

        }

        .dw-footer>span {

            margin: 0;

        }

        .dw-empty {

            display: flex;

            flex-direction: column;

            align-items: center;

            padding: 55px 20px;

            text-align: center;

            border: 1px dashed var(--gray-700);

            border-radius: 14px;

        }

        .dw-empty-icon {

            width: 58px;

            height: 58px;

            margin-bottom: 15px;

            font-size: 27px;

        }

        .dw-empty h3 {

            font-weight: 700;

        }

        .dw-empty p {

            max-width: 390px;

            margin-top: 7px;

            color: var(--gray-400);

            font-size: 13px;

            line-height: 1.7;

        }

        .dw-security {

            display: flex;

            align-items: flex-start;

            gap: 12px;

            padding: 16px;

            border: 1px solid var(--gray-800);

            border-radius: 12px;

            color: var(--primary-400);

        }

        .dw-security>svg {

            width: 21px;

            height: 21px;

            flex-shrink: 0;

        }

        .dw-security strong {

            font-size: 12px;

        }

        .dw-security p {

            margin-top: 4px;

            color: var(--gray-400);

            font-size: 12px;

            line-height: 1.6;

        }

        @media (max-width: 768px) {

            .dw-grid {

                grid-template-columns: 1fr;

                padding: 13px;

            }

            .dw-footer {

                align-items: stretch;

                flex-direction: column;

            }

            .dw-footer button {

                width: 100%;

            }

            .dw-counter {

                margin-left: 0;

            }

        }

        .dw-card-locked {

            opacity: .55;

            cursor: not-allowed;

            border-style: dashed;

            background: var(--gray-950);

        }

        .dw-card-locked:hover {

            border-color: var(--gray-800);

        }

        .dw-card-locked .dw-switch {

            opacity: .4;

            cursor: not-allowed;

        }

        .dw-card-locked .dw-switch input {

            cursor: not-allowed;

        }

        .dw-card-active {

            border-color: var(--primary-500);

            background: color-mix(in srgb,

                    var(--primary-500) 7%,

                    var(--gray-900));

        }

        .dw-permission-ok {

            color: #10b981;

        }

        .dw-permission-denied {

            color: #f87171;

        }
    </style>

</x-filament-panels::page>