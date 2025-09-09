<?php
// app/Models/Cita.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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
        'hora_cita' => 'string', // CAMBIADO: de 'datetime:H:i' a 'string'
        'fecha_hora_cita' => 'datetime',
        'acepta_tratamiento_datos' => 'boolean',
    ];

    // Estado por defecto
    protected $attributes = [
        'estado' => 'programada',
    ];

    const TIPOS_DOCUMENTO = [
        'CC' => 'Cédula de Ciudadanía',
        'CE' => 'Cédula de Extranjería',
        'PA' => 'Pasaporte',
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
        return $this->fecha_cita->format('d/m/Y') . ' a las ' . $this->hora_cita;
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
            // Generar número de cita si no existe
            if (!$cita->numero_cita) {
                $cita->numero_cita = static::generarNumeroCita();
            }

            // Generar fecha_hora_cita combinando fecha y hora
            if ($cita->fecha_cita && $cita->hora_cita) {
                try {
                    // Extraer solo la fecha sin la hora para evitar conflictos
                    $fechaSolo = Carbon::parse($cita->fecha_cita)->format('Y-m-d');
                    $fechaHoraCombinada = $fechaSolo . ' ' . $cita->hora_cita;
                    $cita->fecha_hora_cita = Carbon::parse($fechaHoraCombinada);
                    
                    Log::info('fecha_hora_cita creada correctamente', [
                        'fecha_cita_original' => $cita->fecha_cita,
                        'fecha_solo' => $fechaSolo,
                        'hora_cita' => $cita->hora_cita,
                        'fecha_hora_final' => $cita->fecha_hora_cita
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error creando fecha_hora_cita: ' . $e->getMessage(), [
                        'fecha_cita' => $cita->fecha_cita,
                        'hora_cita' => $cita->hora_cita
                    ]);
                    // Si falla, establecer un valor por defecto
                    $cita->fecha_hora_cita = now();
                }
            }

            // Establecer IP de creación
            $cita->ip_creacion = $cita->ip_creacion ?? request()->ip();

            // Establecer estado por defecto si no existe
            if (!$cita->estado) {
                $cita->estado = 'programada';
            }
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