<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entregas', function (Blueprint $table) {
            $table->string('codigo_snapshot')->nullable();
            $table->string('trabajador_nombre_snapshot')->nullable();
        });

        // Completar las fotografías de las entregas existentes.
        // Se utiliza la información actualmente disponible.
        DB::table('entregas')
            ->select('id', 'codigo_id', 'trabajador_id')
            ->orderBy('id')
            ->chunkById(500, function ($entregas): void {
                $codigoIds = $entregas->pluck('codigo_id')->unique();
                $trabajadorIds = $entregas->pluck('trabajador_id')->unique();

                $codigos = DB::table('codigos')
                    ->whereIn('id', $codigoIds)
                    ->pluck('codigo', 'id');

                $trabajadores = DB::table('trabajadores')
                    ->whereIn('id', $trabajadorIds)
                    ->pluck('nombre', 'id');

                foreach ($entregas as $entrega) {
                    DB::table('entregas')
                        ->where('id', $entrega->id)
                        ->update([
                            'codigo_snapshot' => $codigos->get($entrega->codigo_id),
                            'trabajador_nombre_snapshot' => $trabajadores->get($entrega->trabajador_id),
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('entregas', function (Blueprint $table) {
            $table->dropColumn([
                'codigo_snapshot',
                'trabajador_nombre_snapshot',
            ]);
        });
    }
};
