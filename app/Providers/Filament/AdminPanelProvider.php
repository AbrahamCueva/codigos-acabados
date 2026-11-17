<?php

namespace App\Providers\Filament;

use App\Models\ConfiguracionSistema;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $configuracion = ConfiguracionSistema::query()->first();

        $nombreSistema = $configuracion?->nombre_sistema ?: 'Códigos Acabados';

        $colorPrimario = $configuracion?->color_primario ?: '#F59E0B';

        $logo = $configuracion?->logo_path
            ? Storage::disk('public')->url($configuracion->logo_path)
            : null;

        $logoOscuro = $configuracion?->logo_dark_path
            ? Storage::disk('public')->url($configuracion->logo_dark_path)
            : $logo;

        $favicon = $configuracion?->favicon_path
            ? Storage::disk('public')->url($configuracion->favicon_path)
            : null;

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName($nombreSistema)
            ->brandLogo($logo)
            ->darkModeBrandLogo($logoOscuro)
            ->brandLogoHeight('2rem')
            ->favicon($favicon)
            ->colors([
                'primary' => Color::hex($colorPrimario),
            ])
            ->discoverResources(
                in: app_path('Filament/Resources'),
                for: 'App\Filament\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/Pages'),
                for: 'App\Filament\Pages'
            )
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(
                in: app_path('Filament/Widgets'),
                for: 'App\Filament\Widgets'
            )
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
