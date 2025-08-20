<?php
// database/migrations/xxxx_create_citas_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_cita')->unique();
            $table->foreignId('tramite_id')->constrained()->onDelete('cascade');
            
            // Datos personales
            $table->enum('tipo_documento', ['CC', 'CE', 'PA', 'TI', 'RC']);
            $table->string('numero_documento');
            $table->string('nombres');
            $table->string('apellidos');
            $table->string('email');
            $table->string('telefono');
            $table->string('direccion')->nullable();
            
            // Fecha y hora de la cita
            $table->date('fecha_cita');
            $table->time('hora_cita');
            $table->dateTime('fecha_hora_cita'); // Campo computado para facilitar consultas
            
            // Estado y control
            $table->enum('estado', ['programada', 'confirmada', 'en_proceso', 'atendida', 'cancelada', 'no_asistio'])
                  ->default('programada');
            $table->boolean('acepta_tratamiento_datos')->default(false);
            $table->text('observaciones')->nullable();
            
            // Auditoría
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamp('fecha_actualizacion')->useCurrent()->useCurrentOnUpdate();
            $table->ipAddress('ip_creacion');
            
            $table->timestamps();
            
            // Índices
            $table->index(['fecha_cita', 'hora_cita']);
            $table->index(['tipo_documento', 'numero_documento']);
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};