<?php

namespace App\Filament\Pages;

use App\Models\ConfiguracionSistema as ConfiguracionSistemaModel;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ConfiguracionSistema extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|\UnitEnum|null $navigationGroup = 'Administración';

    protected static ?string $navigationLabel = 'Configuración';

    protected static ?int $navigationSort = 10;

    protected static ?string $title = 'Configuración del sistema';

    protected string $view = 'filament.pages.configuracion-sistema';

    public ?array $data = [];

    private const CAMPOS_IMAGEN = [
        'logo_path',
        'logo_dark_path',
        'favicon_path',
    ];

    private const ETIQUETAS = [
        'nombre_sistema' => 'Nombre del sistema',
        'descripcion' => 'Descripción',
        'nombre_empresa' => 'Nombre de la empresa',
        'logo_path' => 'Logotipo principal',
        'logo_dark_path' => 'Logotipo para modo oscuro',
        'favicon_path' => 'Favicon',
        'color_primario' => 'Color principal',
        'texto_footer' => 'Texto del pie de página',
    ];

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('administrador') ?? false;
    }

    public function mount(): void
    {
        $configuracion = ConfiguracionSistemaModel::actual();

        $this->form->fill($configuracion->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Identidad del sistema')
                    ->description('Configura el nombre y la información general.')
                    ->icon(Heroicon::OutlinedBuildingOffice2)
                    ->columns(2)
                    ->components([
                        TextInput::make('nombre_sistema')
                            ->label('Nombre del sistema')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        TextInput::make('nombre_empresa')
                            ->label('Nombre de la empresa')
                            ->maxLength(255),

                        TextInput::make('descripcion')
                            ->label('Descripción')
                            ->maxLength(255),
                    ]),

                Section::make('Logotipos e identidad gráfica')
                    ->description('Sube las imágenes que identifican al sistema.')
                    ->icon(Heroicon::OutlinedPhoto)
                    ->columns(2)
                    ->components([
                        FileUpload::make('logo_path')
                            ->label('Logotipo principal')
                            ->image()
                            ->disk('public')
                            ->directory('configuracion')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->acceptedFileTypes([
                                'image/png',
                                'image/jpeg',
                                'image/webp',
                            ])
                            ->imagePreviewHeight('120')
                            ->helperText('PNG, JPG o WebP. Máximo 2 MB.'),

                        FileUpload::make('logo_dark_path')
                            ->label('Logotipo para modo oscuro')
                            ->image()
                            ->disk('public')
                            ->directory('configuracion')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->acceptedFileTypes([
                                'image/png',
                                'image/jpeg',
                                'image/webp',
                            ])
                            ->imagePreviewHeight('120')
                            ->helperText('Versión alternativa del logotipo.'),

                        FileUpload::make('favicon_path')
                            ->label('Favicon')
                            ->image()
                            ->disk('public')
                            ->directory('configuracion')
                            ->visibility('public')
                            ->maxSize(1024)
                            ->acceptedFileTypes([
                                'image/png',
                                'image/x-icon',
                                'image/vnd.microsoft.icon',
                            ])
                            ->imagePreviewHeight('80')
                            ->helperText('Icono que aparecerá en la pestaña del navegador.')
                            ->columnSpanFull(),
                    ]),

                Section::make('Apariencia')
                    ->description('Personaliza los colores de la plataforma.')
                    ->icon(Heroicon::OutlinedSwatch)
                    ->columns(2)
                    ->components([
                        ColorPicker::make('color_primario')
                            ->label('Color principal')
                            ->required()
                            ->default('#F5A623')
                            ->helperText('Se requiere un contraste mínimo de 3:1 con el fondo oscuro.')
                            ->columnSpanFull(),
                    ]),

                Section::make('Información del pie de página')
                    ->description('Personaliza el texto inferior del sistema.')
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->components([
                        TextInput::make('texto_footer')
                            ->label('Texto del pie de página')
                            ->maxLength(255)
                            ->placeholder('Todos los derechos reservados.')
                            ->columnSpanFull(),
                    ]),

            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $datos = $this->form->getState();

        $this->validarColor($datos['color_primario'] ?? null);

        $this->persistirConfiguracion(
            $datos,
            'Actualizó la configuración del sistema'
        );

        Notification::make()
            ->title('Configuración actualizada')
            ->body('Los cambios se guardaron correctamente.')
            ->success()
            ->send();

        $this->js('window.location.reload()');
    }

    public function restoreDefaults(): void
    {
        $datos = ConfiguracionSistemaModel::valoresPredeterminados();

        $this->persistirConfiguracion(
            $datos,
            'Restauró la configuración predeterminada del sistema'
        );

        Notification::make()
            ->title('Configuración restaurada')
            ->body('Se recuperaron los valores predeterminados.')
            ->success()
            ->send();

        $this->js('window.location.reload()');
    }

    private function persistirConfiguracion(
        array $datos,
        string $descripcionActividad
    ): void {
        $configuracion = ConfiguracionSistemaModel::actual();

        $anteriores = [];

        foreach (self::CAMPOS_IMAGEN as $campo) {
            $anteriores[$campo] = $configuracion->getOriginal($campo);
        }

        $datos['updated_by_id'] = auth()->id();

        $cambios = [];

        foreach (self::ETIQUETAS as $campo => $etiqueta) {
            $valorAnterior = $configuracion->getOriginal($campo);
            $valorNuevo = $datos[$campo] ?? null;

            if ($valorAnterior !== $valorNuevo) {
                $cambios[$campo] = [
                    'campo' => $etiqueta,
                    'antes' => $valorAnterior,
                    'despues' => $valorNuevo,
                ];
            }
        }

        DB::transaction(function () use ($configuracion, $datos, $cambios, $descripcionActividad): void {
            $configuracion->update($datos);

            if ($cambios !== []) {
                $camposModificados = collect($cambios)
                    ->pluck('campo')
                    ->implode(', ');

                activity('configuracion')
                    ->causedBy(auth()->user())
                    ->performedOn($configuracion)
                    ->withProperties([
                        'cambios' => $cambios,
                    ])
                    ->log("{$descripcionActividad}: {$camposModificados}");
            }
        });

        $nuevas = [];

        foreach (self::CAMPOS_IMAGEN as $campo) {
            $nuevas[$campo] = $configuracion->fresh()->getAttribute($campo);
        }

        $this->eliminarImagenesObsoletas($anteriores, $nuevas);
    }

    private function eliminarImagenesObsoletas(
        array $anteriores,
        array $nuevas
    ): void {
        $rutasNuevas = array_filter(
            array_values($nuevas),
            fn($ruta) => is_string($ruta) && $ruta !== ''
        );

        $rutasAnteriores = array_unique(array_filter(
            array_values($anteriores),
            fn($ruta) => is_string($ruta) && $ruta !== ''
        ));

        foreach ($rutasAnteriores as $ruta) {
            if (in_array($ruta, $rutasNuevas, true)) {
                continue;
            }

            // Solo se eliminan archivos dentro de la carpeta propia.
            if (!str_starts_with($ruta, 'configuracion/')) {
                continue;
            }

            Storage::disk('public')->delete($ruta);
        }
    }

    private function validarColor(?string $color): void
    {
        if (
            !is_string($color) ||
            !preg_match('/^#[0-9A-Fa-f]{6}$/', $color)
        ) {
            throw ValidationException::withMessages([
                'data.color_primario' => 'El color debe tener un formato hexadecimal válido, por ejemplo #F5A623.',
            ]);
        }

        $luminanciaColor = $this->obtenerLuminancia($color);
        $luminanciaFondo = $this->obtenerLuminancia('#111111');

        $contraste = (
            max($luminanciaColor, $luminanciaFondo) + 0.05
        ) / (
            min($luminanciaColor, $luminanciaFondo) + 0.05
        );

        if ($contraste < 3) {
            throw ValidationException::withMessages([
                'data.color_primario' => 'El color es demasiado oscuro. Selecciona uno con contraste mínimo de 3:1 respecto al fondo.',
            ]);
        }
    }

    private function obtenerLuminancia(string $hex): float
    {
        $hex = ltrim($hex, '#');

        $canales = [
            hexdec(substr($hex, 0, 2)) / 255,
            hexdec(substr($hex, 2, 2)) / 255,
            hexdec(substr($hex, 4, 2)) / 255,
        ];

        $canales = array_map(
            fn(float $canal): float => $canal <= 0.04045
                ? $canal / 12.92
                : (($canal + 0.055) / 1.055) ** 2.4,
            $canales
        );

        return (
            0.2126 * $canales[0] +
            0.7152 * $canales[1] +
            0.0722 * $canales[2]
        );
    }
}
