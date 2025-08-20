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

    public function getHorasDisponibles(Carbon $fecha): array
    {
        $horas = [];
        $horaInicio = Carbon::parse($this->hora_inicio);
        $horaFin = Carbon::parse($this->hora_fin);
        
        while ($horaInicio->lt($horaFin)) {
            $horas[] = $horaInicio->format('H:i');
            $horaInicio->addHour();
        }
        
        return $horas;
    }
}