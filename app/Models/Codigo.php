<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Codigo extends Model
{
    use LogsActivity;

    protected $table = 'codigos';

    protected $fillable = [
        'codigo',
        'stock',
    ];

    protected function casts(): array
    {
        return [
            'stock' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        // Integridad: un código con entregas registradas no se elimina.
        static::deleting(function (Codigo $codigo): void {
            if ($codigo->tieneEntregas()) {
                throw new \RuntimeException(
                    'No se puede eliminar un código que tiene entregas registradas.'
                );
            }
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('codigos')
            ->logOnly(['codigo', 'stock'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(
                fn (string $evento): string => 'Código ' . $this->codigo . ' ' . match ($evento) {
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
