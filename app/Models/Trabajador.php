<?php

namespace App\Models;

use App\Enums\TipoTrabajador;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Trabajador extends Model
{
    use LogsActivity;

    protected $table = 'trabajadores';

    protected $fillable = ['nombre', 'tipo', 'activo'];

    protected function casts(): array
    {
        return [
            'tipo' => TipoTrabajador::class,
            'activo' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // Integridad: un trabajador con entregas registradas no se elimina (se desactiva).
        static::deleting(fn(Trabajador $trabajador): bool => !$trabajador->tieneEntregas());
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('trabajadores')
            ->logOnly(['nombre', 'tipo', 'activo'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(
                fn(string $evento): string => 'Trabajador ' . $this->nombre . ' ' . match ($evento) {
                    'created' => 'registrado',
                    'updated' => 'modificado',
                    'deleted' => 'eliminado',
                    default => $evento,
                }
            );
    }

    public function entregas(): HasMany
    {
        return $this->hasMany(Entrega::class);
    }

    public function tieneEntregas(): bool
    {
        return $this->entregas()->exists();
    }
}