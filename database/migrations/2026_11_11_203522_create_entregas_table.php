<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('entregas', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->foreignId('codigo_id')->constrained('codigos')->restrictOnDelete();
            $table->foreignId('trabajador_id')->constrained('trabajadores')->restrictOnDelete();
            $table->unsignedInteger('rollos_entregados');
            $table->unsignedInteger('rollos_restantes');
            $table->timestamps();

            $table->index('fecha');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entregas');
    }
};
