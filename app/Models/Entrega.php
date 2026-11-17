<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\Activitylog\Contracts\Activity as ActivityContract;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Entrega extends Model
{
    use LogsActivity;

    protected $table = 'entregas';

    protected $fillable = [
        'fecha',
        'codigo_id',
        'trabajador_id',
        'rollos_entregados',
        'rollos_restantes',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'rollos_entregados' => 'integer',
            'rollos_restantes' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        /*
         * CREACIÓN
         *
         * Bloquea el código y al trabajador para evitar inconsistencias
         * cuando existen operaciones simultáneas.
         */
        static::creating(function (Entrega $entrega): void {

            // Validar cantidad.
            $cantidad = filter_var(
                $entrega->rollos_entregados,
                FILTER_VALIDATE_INT
            );

            if ($cantidad === false || $cantidad < 1) {
                throw ValidationException::withMessages([
                    'data.rollos_entregados' => 'La cantidad de rollos debe ser un entero mayor que cero.',
                ]);
            }

            // Bloquear código y comprobar stock.
            $codigo = Codigo::query()
                ->lockForUpdate()
                ->find($entrega->codigo_id);

            if (!$codigo) {
                throw ValidationException::withMessages([
                    'data.codigo_id' => 'El código seleccionado no existe.',
                ]);
            }

            if ($codigo->stock < $cantidad) {
                throw ValidationException::withMessages([
                    'data.rollos_entregados' => 'No hay stock suficiente para este código. Stock disponible: ' . $codigo->stock . '.',
                ]);
            }

            // Bloquear trabajador y comprobar su estado.
            $trabajador = Trabajador::query()
                ->lockForUpdate()
                ->find($entrega->trabajador_id);

            if (!$trabajador) {
                throw ValidationException::withMessages([
                    'data.trabajador_id' => 'El trabajador seleccionado no existe.',
                ]);
            }

            if (!$trabajador->activo) {
                throw ValidationException::withMessages([
                    'data.trabajador_id' => 'No se pueden registrar entregas a trabajadores inactivos.',
                ]);
            }

            // Guardar fotografías históricas.
            $entrega->codigo_snapshot = $codigo->codigo;
            $entrega->trabajador_nombre_snapshot = $trabajador->nombre;

            // Descontar stock.
            $codigo->decrement('stock', $cantidad);

            // Guardar el stock restante después de la entrega.
            $entrega->rollos_restantes = $codigo->stock;
        });

        /*
         * ACTUALIZACIÓN
         *
         * El código y las cantidades no pueden modificarse.
         * Para corregirlos, se debe eliminar y registrar nuevamente.
         */
        static::updating(function (Entrega $entrega): void {

            if ($entrega->isDirty('codigo_id')) {
                throw ValidationException::withMessages([
                    'data.codigo_id' => 'No puedes cambiar el código de una entrega registrada. Elimina la entrega y créala nuevamente.',
                ]);
            }

            if (
                $entrega->isDirty('rollos_entregados') ||
                $entrega->isDirty('rollos_restantes')
            ) {
                throw ValidationException::withMessages([
                    'data.rollos_entregados' => 'No puedes modificar las cantidades de una entrega registrada. Elimina la entrega y créala nuevamente.',
                ]);
            }

            // Si cambia el trabajador, actualizar su fotografía histórica.
            if ($entrega->isDirty('trabajador_id')) {
                $trabajador = Trabajador::query()
                    ->find($entrega->trabajador_id);

                if (!$trabajador) {
                    throw ValidationException::withMessages([
                        'data.trabajador_id' => 'El trabajador seleccionado no existe.',
                    ]);
                }

                $entrega->trabajador_nombre_snapshot = $trabajador->nombre;
            }
        });

        /*
         * ELIMINACIÓN
         *
         * Restaurar el stock únicamente después de eliminar la entrega.
         */
        static::deleted(function (Entrega $entrega): void {

            Codigo::query()
                ->whereKey($entrega->codigo_id)
                ->increment('stock', $entrega->rollos_entregados);

        });
    }

    /**
     * Configuración del registro de actividades.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('entregas')
            ->logOnly([
                'fecha',
                'codigo_id',
                'trabajador_id',
                'rollos_entregados',
                'rollos_restantes',
            ])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(
                fn (string $evento): string => sprintf(
                    'Entrega %s: código %s, trabajador %s, %d rollo(s)',
                    match ($evento) {
                        'created' => 'registrada',
                        'updated' => 'modificada',
                        'deleted' => 'eliminada',
                        default => $evento,
                    },
                    $this->codigo_snapshot ?? $this->codigo?->codigo ?? '—',
                    $this->trabajador_nombre_snapshot ?? $this->trabajador?->nombre ?? '—',
                    $this->rollos_entregados,
                )
            );
    }

    /**
     * Guardar dentro de una transacción.
     */
    public function save(array $options = []): bool
    {
        return DB::transaction(
            fn (): bool => parent::save($options)
        );
    }

    /**
     * Eliminar dentro de una transacción.
     * Bloquea el código antes de restaurar el stock.
     */
    public function delete(): ?bool
    {
        return DB::transaction(function (): ?bool {

            $codigo = Codigo::query()
                ->lockForUpdate()
                ->find($this->codigo_id);

            if (!$codigo) {
                throw ValidationException::withMessages([
                    'data.codigo_id' => 'No se encontró el código asociado a esta entrega.',
                ]);
            }

            return parent::delete();
        });
    }

    public function codigo(): BelongsTo
    {
        return $this->belongsTo(Codigo::class);
    }

    public function trabajador(): BelongsTo
    {
        return $this->belongsTo(Trabajador::class);
    }

    /**
     * Guarda los nombres relacionados en el historial de actividades.
     */
    public function beforeActivityLogged(
        ActivityContract $activity,
        string $eventName
    ): void {
        $cambios = $activity->attribute_changes?->toArray() ?? [];

        $snapshot = [];

        foreach (['attributes', 'old'] as $lado) {
            $valores = $cambios[$lado] ?? [];

            if (array_key_exists('codigo_id', $valores)) {
                $snapshot[$lado]['codigo'] = Codigo::query()
                    ->whereKey($valores['codigo_id'])
                    ->value('codigo');
            }

            if (array_key_exists('trabajador_id', $valores)) {
                $snapshot[$lado]['trabajador'] = Trabajador::query()
                    ->whereKey($valores['trabajador_id'])
                    ->value('nombre');
            }
        }

        $properties = collect($activity->properties ?? []);

        $activity->properties = $properties->put('snapshot', $snapshot);
    }
}
