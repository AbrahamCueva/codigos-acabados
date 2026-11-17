<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConfiguracionSistema extends Model
{
    protected $table = 'configuraciones_sistema';

    protected $fillable = [
        'nombre_sistema',
        'descripcion',
        'nombre_empresa',
        'logo_path',
        'logo_dark_path',
        'favicon_path',
        'color_primario',
        'texto_footer',
        'updated_by_id',
    ];

    /**
     * Obtener la configuración global.
     * Si todavía no existe, crearla con los valores predeterminados.
     */
    public static function actual(): self
    {
        return static::query()->firstOrCreate(
            ['id' => 1],
            [
                'nombre_sistema' => 'Códigos Acabados',
                'descripcion' => 'Sistema de gestión interna',
                'nombre_empresa' => 'CottonKnit',
                'color_primario' => '#F5A623',
                'texto_footer' => 'Todos los derechos reservados.',
            ]
        );
    }

    public function actualizadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

    public static function valoresPredeterminados(): array
    {
        return [
            'nombre_sistema' => 'Códigos Acabados',
            'descripcion' => 'Sistema de gestión interna',
            'nombre_empresa' => 'CottonKnit',
            'logo_path' => null,
            'logo_dark_path' => null,
            'favicon_path' => null,
            'color_primario' => '#F5A623',
            'texto_footer' => 'Todos los derechos reservados.',
        ];
    }
}
