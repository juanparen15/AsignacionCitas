<?php
// app/Models/Cita.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Cita extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_cita',
        'tramite_id',
        'tipo_documento',
        'numero_documento',
        'nombres',
        'apellidos',
        'email',
        'telefono',
        'direccion',
        'fecha_cita',
        'hora_cita',
        'fecha_hora_cita',
        'estado',
        'acepta_tratamiento_datos',
        'observaciones',
        'ip_creacion',
    ];

    protected $casts = [
        'fecha_cita' => 'date',
        'hora_cita' => 'datetime:H:i',
        'fecha_hora_cita' => 'datetime',
        'acepta_tratamiento_datos' => 'boolean',
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime',
    ];

    const TIPOS_DOCUMENTO = [
        'CC' => 'Cédula de Ciudadanía',
        'CE' => 'Cédula de Extranjería',
        'PA' => 'Pasaporte',
        'TI' => 'Tarjeta de Identidad',
        'RC' => 'Registro Civil',
    ];

    const ESTADOS = [
        'programada' => 'Programada',
        'confirmada' => 'Confirmada',
        'en_proceso' => 'En Proceso',
        'atendida' => 'Atendida',
        'cancelada' => 'Cancelada',
        'no_asistio' => 'No Asistió',
    ];

    public function tramite(): BelongsTo
    {
        return $this->belongsTo(Tramite::class);
    }

    public function getNombreCompletoAttribute(): string
    {
        return $this->nombres . ' ' . $this->apellidos;
    }

    public function getDocumentoCompletoAttribute(): string
    {
        return self::TIPOS_DOCUMENTO[$this->tipo_documento] . ' ' . $this->numero_documento;
    }

    public function getEstadoLabelAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }

    public function getFechaHoraFormateadaAttribute(): string
    {
        return $this->fecha_cita->format('d/m/Y') . ' a las ' . $this->hora_cita->format('H:i');
    }

    public function scopeProgramadas($query)
    {
        return $query->whereIn('estado', ['programada', 'confirmada']);
    }

    public function scopeParaFecha($query, $fecha)
    {
        return $query->whereDate('fecha_cita', $fecha);
    }

    public function scopeParaHora($query, $hora)
    {
        return $query->whereTime('hora_cita', $hora);
    }

    // Generar número de cita único
    protected static function booted()
    {
        static::creating(function ($cita) {
            $cita->numero_cita = static::generarNumeroCita();
            $cita->fecha_hora_cita = Carbon::parse($cita->fecha_cita->format('Y-m-d') . ' ' . $cita->hora_cita->format('H:i:s'));
            $cita->ip_creacion = request()->ip();
        });
    }

    private static function generarNumeroCita(): string
    {
        do {
            $numero = 'CT-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (static::where('numero_cita', $numero)->exists());

        return $numero;
    }
}