<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuraciones_sistema', function (Blueprint $table) {
            $table->id();

            // Identidad
            $table->string('nombre_sistema')->default('Códigos Acabados');
            $table->string('descripcion')->nullable();
            $table->string('nombre_empresa')->nullable();

            // Recursos gráficos
            $table->string('logo_path')->nullable();
            $table->string('logo_dark_path')->nullable();
            $table->string('favicon_path')->nullable();

            // Apariencia
            $table->string('color_primario', 7)->default('#F5A623');

            // Información adicional
            $table->string('texto_footer')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuraciones_sistema');
    }
};
