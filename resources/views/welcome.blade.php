
@php
    use App\Models\ConfiguracionSistema;
    use Illuminate\Support\Facades\Storage;

    $configuracion = ConfiguracionSistema::actual();

    $nombreSistema = $configuracion->nombre_sistema ?: 'Códigos Acabados';
    $descripcion = $configuracion->descripcion ?: 'Sistema de gestión interna';
    $nombreEmpresa = $configuracion->nombre_empresa ?: $nombreSistema;
    $colorPrimario = $configuracion->color_primario ?: '#F5A623';

    $logoUrl = $configuracion->logo_path
        ? Storage::disk('public')->url($configuracion->logo_path)
        : null;

    $faviconUrl = $configuracion->favicon_path
        ? Storage::disk('public')->url($configuracion->favicon_path)
        : asset('favicon.ico');

    $textoFooter = $configuracion->texto_footer ?: 'Todos los derechos reservados.';

    $autenticado = auth()->guard('web')->check();
    $urlAcceso = $autenticado ? url('/admin') : url('/admin/login');
    $textoAcceso = $autenticado ? 'Ir al panel' : 'Iniciar sesión';
@endphp

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="{{ $colorPrimario }}">

    <title>{{ $nombreSistema }} | Sistema de Gestión</title>

    <meta name="description" content="{{ $descripcion }}">

    <link rel="icon" href="{{ $faviconUrl }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Manrope:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --background: #111111;
            --surface: #191919;
            --primary: {{ $colorPrimario }};
            --text: #f5f5f5;
            --muted: #999999;
            --border: #303030;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: var(--background);
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .container {
            width: min(1100px, calc(100% - 48px));
            margin: auto;
        }

        header {
            height: 85px;
            border-bottom: 1px solid var(--border);
        }

        .navbar {
            height: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-icon {
            width: 42px;
            height: 42px;
            display: grid;
            place-items: center;
            overflow: hidden;
            border-radius: 9px;
            background: var(--primary);
            color: #111;
            font-family: 'Manrope', sans-serif;
            font-size: 23px;
            font-weight: 800;
        }

        .brand-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .brand-name {
            font-family: 'Manrope', sans-serif;
            font-size: 16px;
            font-weight: 800;
        }

        .brand-subtitle {
            display: block;
            margin-top: 3px;
            color: var(--muted);
            font-size: 10px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .access-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            padding: 12px 19px;
            border-radius: 8px;
            background: var(--primary);
            color: #151515;
            font-size: 13px;
            font-weight: 800;
            transition: filter .2s, transform .2s;
        }

        .access-button:hover {
            filter: brightness(1.12);
            transform: translateY(-2px);
        }

        main {
            flex: 1;
            display: flex;
            align-items: center;
        }

        .hero {
            padding-top: 100px;
            padding-bottom: 110px;
        }

        .eyebrow {
            color: var(--primary);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        h1 {
            max-width: 750px;
            margin-top: 25px;
            font-family: 'Manrope', sans-serif;
            font-size: clamp(42px, 6vw, 70px);
            font-weight: 800;
            letter-spacing: -3px;
            line-height: 1.12;
        }

        h1 span {
            color: var(--primary);
        }

        .description {
            max-width: 600px;
            margin-top: 25px;
            color: var(--muted);
            font-size: 16px;
            line-height: 1.9;
        }

        .hero-actions {
            margin-top: 35px;
        }

        .hero-actions .access-button {
            padding: 15px 24px;
            font-size: 14px;
        }

        footer {
            border-top: 1px solid var(--border);
        }

        .footer-content {
            min-height: 75px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            color: var(--muted);
            font-size: 12px;
        }

        .footer-content strong {
            color: var(--text);
        }

        @media (max-width: 600px) {
            .container {
                width: calc(100% - 36px);
            }

            header {
                height: 75px;
            }

            .brand-name {
                font-size: 13px;
            }

            .brand-icon {
                width: 36px;
                height: 36px;
            }

            .access-button {
                padding: 10px 12px;
                font-size: 11px;
            }

            .hero {
                padding-top: 75px;
                padding-bottom: 85px;
            }

            h1 {
                font-size: 43px;
                letter-spacing: -2px;
            }

            .description {
                font-size: 14px;
            }

            .footer-content {
                padding: 20px 0;
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>

<body>

    <header>
        <div class="container navbar">

            <a href="{{ route('home') }}" class="brand">
                <div class="brand-icon">
                    @if ($logoUrl)
                        <img src="{{ $logoUrl }}" alt="{{ $nombreSistema }}">
                    @else
                        {{ mb_substr($nombreSistema, 0, 1) }}
                    @endif
                </div>

                <div>
                    <div class="brand-name">{{ $nombreSistema }}</div>
                    <span class="brand-subtitle">{{ $descripcion }}</span>
                </div>
            </a>

            <a href="{{ $urlAcceso }}" class="access-button">
                {{ $textoAcceso }}
                <span>→</span>
            </a>

        </div>
    </header>

    <main>
        <section class="container hero">

            <div class="eyebrow">
                {{ $descripcion }}
            </div>

            <h1>
                Control y gestión
                <span>de acabados.</span>
            </h1>

            <p class="description">
                Plataforma diseñada para administrar códigos,
                controlar inventarios, registrar entregas y
                realizar el seguimiento de las operaciones
                del área de Acabados.
            </p>

            <div class="hero-actions">
                <a href="{{ $urlAcceso }}" class="access-button">
                    {{ $textoAcceso }}
                    <span>→</span>
                </a>
            </div>

        </section>
    </main>

    <footer>
        <div class="container footer-content">

            <div>
                <strong>{{ $nombreSistema }}</strong>
                · {{ $nombreEmpresa }}
            </div>

            <div>
                © {{ date('Y') }} · {{ $textoFooter }}
            </div>

        </div>
    </footer>

</body>
</html>
