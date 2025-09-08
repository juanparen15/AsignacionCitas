<?php
// app/Models/ConfiguracionTramite.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ConfiguracionTramite extends Model
{
    use HasFactory;

    protected $fillable = [
        'tramite_id',
        'hora_inicio',
        'hora_fin',
        'dias_disponibles',
        'citas_por_hora',
        'dias_anticipacion_minima',
        'dias_anticipacion_maxima',
        'dias_inhabiles',
        'requiere_documentos',
        'documentos_requeridos',
    ];

    protected $casts = [
        'hora_inicio' => 'datetime:H:i',
        'hora_fin' => 'datetime:H:i',
        'dias_disponibles' => 'array',
        'citas_por_hora' => 'integer',
        'dias_anticipacion_minima' => 'integer',
        'dias_anticipacion_maxima' => 'integer',
        'dias_inhabiles' => 'array',
        'requiere_documentos' => 'boolean',
        'documentos_requeridos' => 'array',
    ];

    // Horario de almuerzo configurables
    const HORA_INICIO_ALMUERZO = '12:00';
    const HORA_FIN_ALMUERZO = '14:00';

    public function tramite(): BelongsTo
    {
        return $this->belongsTo(Tramite::class);
    }

    public function getFechaMinimaCitaAttribute(): Carbon
    {
        return Carbon::now()->addDays($this->dias_anticipacion_minima);
    }

    public function getFechaMaximaCitaAttribute(): Carbon
    {
        return Carbon::now()->addDays($this->dias_anticipacion_maxima);
    }

    public function isDiaDisponible(Carbon $fecha): bool
    {
        $diaSemana = $fecha->dayOfWeek === 0 ? 7 : $fecha->dayOfWeek; // Convertir domingo de 0 a 7
        return in_array((string)$diaSemana, $this->dias_disponibles);
    }

    public function isDiaInhabil(Carbon $fecha): bool
    {
        if (!$this->dias_inhabiles) {
            return false;
        }
        
        return in_array($fecha->format('Y-m-d'), $this->dias_inhabiles);
    }

    /**
     * Obtiene las horas disponibles excluyendo el horario de almuerzo
     */
    public function getHorasDisponibles(Carbon $fecha): array
    {
        $horas = [];
        $horaInicio = Carbon::parse($this->hora_inicio);
        $horaFin = Carbon::parse($this->hora_fin);
        
        // Horas de almuerzo
        $horaInicioAlmuerzo = Carbon::parse(self::HORA_INICIO_ALMUERZO);
        $horaFinAlmuerzo = Carbon::parse(self::HORA_FIN_ALMUERZO);
        
        $horaActual = $horaInicio->copy();
        
        while ($horaActual->lt($horaFin)) {
            // Verificar si la hora actual está en el rango de almuerzo
            if (!$this->esHorarioAlmuerzo($horaActual, $horaInicioAlmuerzo, $horaFinAlmuerzo)) {
                $horas[] = $horaActual->format('H:i');
            }
            
            $horaActual->addHour();
        }
        
        return $horas;
    }

    /**
     * Verifica si una hora está en el horario de almuerzo
     */
    private function esHorarioAlmuerzo(Carbon $hora, Carbon $inicioAlmuerzo, Carbon $finAlmuerzo): bool
    {
        return $hora->gte($inicioAlmuerzo) && $hora->lt($finAlmuerzo);
    }

    /**
     * Obtiene las horas disponibles con intervalos personalizados (15, 30, 60 minutos)
     */
    public function getHorasDisponiblesConIntervalo(Carbon $fecha, int $intervaloMinutos = 60): array
    {
        $horas = [];
        $horaInicio = Carbon::parse($this->hora_inicio);
        $horaFin = Carbon::parse($this->hora_fin);
        
        // Horas de almuerzo
        $horaInicioAlmuerzo = Carbon::parse(self::HORA_INICIO_ALMUERZO);
        $horaFinAlmuerzo = Carbon::parse(self::HORA_FIN_ALMUERZO);
        
        $horaActual = $horaInicio->copy();
        
        while ($horaActual->lt($horaFin)) {
            // Verificar si la hora actual está en el rango de almuerzo
            if (!$this->esHorarioAlmuerzo($horaActual, $horaInicioAlmuerzo, $horaFinAlmuerzo)) {
                // Verificar que haya tiempo suficiente antes del almuerzo o después
                if ($this->tieneEspacioSuficiente($horaActual, $horaInicioAlmuerzo, $horaFinAlmuerzo, $intervaloMinutos)) {
                    $horas[] = $horaActual->format('H:i');
                }
            }
            
            $horaActual->addMinutes($intervaloMinutos);
        }
        
        return $horas;
    }

    /**
     * Verifica si hay espacio suficiente para la cita considerando el horario de almuerzo
     */
    private function tieneEspacioSuficiente(Carbon $hora, Carbon $inicioAlmuerzo, Carbon $finAlmuerzo, int $duracionMinutos): bool
    {
        $finCita = $hora->copy()->addMinutes($duracionMinutos);
        
        // Si la cita termina antes del almuerzo o comienza después del almuerzo, está bien
        if ($finCita->lte($inicioAlmuerzo) || $hora->gte($finAlmuerzo)) {
            return true;
        }
        
        // Si la cita se superpone con el almuerzo, no es válida
        return false;
    }

    /**
     * Obtiene información del horario de almuerzo
     */
    public static function getHorarioAlmuerzo(): array
    {
        return [
            'inicio' => self::HORA_INICIO_ALMUERZO,
            'fin' => self::HORA_FIN_ALMUERZO,
            'mensaje' => 'Horario de almuerzo no disponible para citas'
        ];
    }

    /**
     * Verifica si una hora específica está disponible (no es horario de almuerzo)
     */
    public function isHoraDisponible(string $hora): bool
    {
        $horaCarbon = Carbon::parse($hora);
        $horaInicioAlmuerzo = Carbon::parse(self::HORA_INICIO_ALMUERZO);
        $horaFinAlmuerzo = Carbon::parse(self::HORA_FIN_ALMUERZO);
        
        return !$this->esHorarioAlmuerzo($horaCarbon, $horaInicioAlmuerzo, $horaFinAlmuerzo);
    }
}