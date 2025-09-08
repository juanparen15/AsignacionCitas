<?php
// database/migrations/xxxx_create_configuracion_tramites_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracion_tramites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tramite_id')->constrained()->onDelete('cascade');
            $table->time('hora_inicio')->nullable()->default('08:00:00');
            $table->time('hora_fin')->nullable()->default('17:00:00');
            $table->json('dias_disponibles')->nullable(); // 1=Lunes, 7=Domingo
            $table->integer('citas_por_hora')->nullable()->default(4);
            $table->integer('dias_anticipacion_minima')->nullable()->default(1);
            $table->integer('dias_anticipacion_maxima')->nullable()->default(30);
            $table->json('dias_inhabiles')->nullable(); // Fechas específicas inhabiles
            $table->boolean('requiere_documentos')->nullable()->default(false);
            $table->json('documentos_requeridos')->nullable();
            $table->timestamps();
            $table->unique('tramite_id');
            $table->index(['hora_inicio', 'hora_fin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion_tramites');
    }
};
