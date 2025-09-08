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
     * Obtiene las horas disponibles excluyendo el horario de almuerzo y horas pasadas
     * Esta es la función principal que filtra las horas
     */
    public function getHorasDisponibles(Carbon $fecha): array
    {
        $horas = [];
        $horaInicio = Carbon::parse($this->hora_inicio);
        $horaFin = Carbon::parse($this->hora_fin);

        // Horas de almuerzo
        $horaInicioAlmuerzo = Carbon::parse(self::HORA_INICIO_ALMUERZO);
        $horaFinAlmuerzo = Carbon::parse(self::HORA_FIN_ALMUERZO);

        // Verificar si la fecha es hoy para filtrar horas pasadas
        $esHoy = $fecha->isToday();
        $ahora = now();

        $horaActual = $horaInicio->copy();

        while ($horaActual->lt($horaFin)) {
            // Verificar si la hora actual NO está en el rango de almuerzo
            if (!$this->esHorarioAlmuerzo($horaActual, $horaInicioAlmuerzo, $horaFinAlmuerzo)) {

                // Si es hoy, solo incluir horas futuras con validación estricta
                if ($esHoy) {
                    // Crear un objeto Carbon para la hora específica de hoy
                    $horaEspecificaHoy = Carbon::today()->setTimeFromTimeString($horaActual->format('H:i'));

                    // MEJORA: Verificar que la hora sea al menos 1 hora después de ahora
                    // Y que no sea una hora que ya pasó
                    if (
                        $horaEspecificaHoy->gt($ahora->copy()->addHour()) &&
                        $horaEspecificaHoy->gt($ahora)
                    ) {
                        $horas[] = $horaActual->format('H:i');
                    }
                } else {
                    // Si NO es hoy, incluir todas las horas válidas
                    $horas[] = $horaActual->format('H:i');
                }
            }

            $horaActual->addHour();
        }

        return $horas;
    }
    /**
     * NUEVO: Método específico para validar si una hora está disponible para una fecha dada
     */
    public function isHoraDisponibleParaFecha(string $hora, Carbon $fecha): bool
    {
        $horaCarbon = Carbon::parse($hora);
        $horaInicioAlmuerzo = Carbon::parse(self::HORA_INICIO_ALMUERZO);
        $horaFinAlmuerzo = Carbon::parse(self::HORA_FIN_ALMUERZO);

        // Verificar que no sea horario de almuerzo
        $noEsAlmuerzo = !$this->esHorarioAlmuerzo($horaCarbon, $horaInicioAlmuerzo, $horaFinAlmuerzo);

        // Verificar que esté dentro del horario de atención
        $dentroDelHorario = $horaCarbon->gte(Carbon::parse($this->hora_inicio)) &&
            $horaCarbon->lt(Carbon::parse($this->hora_fin));

        // Si es hoy, verificar que no sea una hora pasada
        if ($fecha->isToday()) {
            $horaEspecificaHoy = Carbon::today()->setTimeFromTimeString($hora);
            $ahora = now();

            // Debe ser al menos 1 hora después de ahora y no una hora que ya pasó
            $noEsHoraPasada = $horaEspecificaHoy->gt($ahora->copy()->addHour()) &&
                $horaEspecificaHoy->gt($ahora);

            return $noEsAlmuerzo && $dentroDelHorario && $noEsHoraPasada;
        }

        // Si no es hoy, solo verificar almuerzo y horario
        return $noEsAlmuerzo && $dentroDelHorario;
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
     * También filtra horas pasadas si es hoy
     */
    public function getHorasDisponiblesConIntervalo(Carbon $fecha, int $intervaloMinutos = 60): array
    {
        $horas = [];
        $horaInicio = Carbon::parse($this->hora_inicio);
        $horaFin = Carbon::parse($this->hora_fin);

        // Horas de almuerzo
        $horaInicioAlmuerzo = Carbon::parse(self::HORA_INICIO_ALMUERZO);
        $horaFinAlmuerzo = Carbon::parse(self::HORA_FIN_ALMUERZO);

        // Verificar si la fecha es hoy para filtrar horas pasadas
        $esHoy = $fecha->isToday();
        $ahora = now();

        $horaActual = $horaInicio->copy();

        while ($horaActual->lt($horaFin)) {
            // Verificar si la hora actual NO está en el rango de almuerzo
            if (!$this->esHorarioAlmuerzo($horaActual, $horaInicioAlmuerzo, $horaFinAlmuerzo)) {
                // Verificar que haya tiempo suficiente antes del almuerzo o después
                if ($this->tieneEspacioSuficiente($horaActual, $horaInicioAlmuerzo, $horaFinAlmuerzo, $intervaloMinutos)) {

                    // Si es hoy, solo incluir horas futuras con anticipación
                    if ($esHoy) {
                        $horaEspecificaHoy = Carbon::today()->setTimeFromTimeString($horaActual->format('H:i'));

                        // MEJORA: Verificar que no sea hora pasada y tenga anticipación mínima
                        if (
                            $horaEspecificaHoy->gt($ahora->copy()->addMinutes(60)) &&
                            $horaEspecificaHoy->gt($ahora)
                        ) {
                            $horas[] = $horaActual->format('H:i');
                        }
                    } else {
                        // Si NO es hoy, incluir todas las horas válidas
                        $horas[] = $horaActual->format('H:i');
                    }
                }
            }

            $horaActual->addMinutes($intervaloMinutos);
        }

        return $horas;
    }

    /**
     * NUEVO: Método para obtener la hora mínima disponible para hoy
     */
    public function getHoraMinimaHoy(): ?string
    {
        $ahora = now();
        $horaMinima = $ahora->copy()->addHour()->ceilMinute(60); // Redondear a la siguiente hora

        // Si ya pasó el horario de atención de hoy, retornar null
        $horaFinHoy = Carbon::today()->setTimeFromTimeString($this->hora_fin);
        if ($horaMinima->gte($horaFinHoy)) {
            return null;
        }

        return $horaMinima->format('H:i');
    }

    /**
     * NUEVO: Método para obtener mensaje explicativo sobre disponibilidad
     */
    public function getMensajeDisponibilidad(Carbon $fecha): string
    {
        if ($fecha->isToday()) {
            $horaMinima = $this->getHoraMinimaHoy();
            if ($horaMinima === null) {
                return "No hay horarios disponibles para hoy. Seleccione otra fecha.";
            }
            return "Para hoy, solo están disponibles horarios desde las {$horaMinima}.";
        }

        $horarioAlmuerzo = self::getHorarioAlmuerzo();
        return "Horarios disponibles excluyendo almuerzo ({$horarioAlmuerzo['inicio']} - {$horarioAlmuerzo['fin']}).";
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
     * Verifica si una hora específica está disponible (no es horario de almuerzo ni hora pasada)
     */
    public function isHoraDisponible(string $hora, Carbon $fecha = null): bool
    {
        $horaCarbon = Carbon::parse($hora);
        $horaInicioAlmuerzo = Carbon::parse(self::HORA_INICIO_ALMUERZO);
        $horaFinAlmuerzo = Carbon::parse(self::HORA_FIN_ALMUERZO);

        // Verificar que no sea horario de almuerzo
        $noEsAlmuerzo = !$this->esHorarioAlmuerzo($horaCarbon, $horaInicioAlmuerzo, $horaFinAlmuerzo);

        // Si se proporciona fecha y es hoy, verificar que no sea hora pasada
        if ($fecha && $fecha->isToday()) {
            $horaEspecificaHoy = Carbon::today()->setTimeFromTimeString($hora);
            $noEsHoraPasada = $horaEspecificaHoy->gt(now()->addHour());

            return $noEsAlmuerzo && $noEsHoraPasada;
        }

        return $noEsAlmuerzo;
    }
}
