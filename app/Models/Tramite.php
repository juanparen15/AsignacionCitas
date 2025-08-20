<?php
// app/Models/Tramite.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tramite extends Model
{
    use HasFactory;

    protected $fillable = [
        'area_id',
        'nombre',
        'descripcion',
        'requisitos',
        'costo',
        'es_gratuito',
        'duracion_minutos',
        'activo',
        'orden',
    ];

    protected $casts = [
        'costo' => 'decimal:2',
        'es_gratuito' => 'boolean',
        'duracion_minutos' => 'integer',
        'activo' => 'boolean',
        'orden' => 'integer',
    ];

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function configuracion(): HasOne
    {
        return $this->hasOne(ConfiguracionTramite::class);
    }

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class);
    }

    public function citasActivas(): HasMany
    {
        return $this->citas()->whereIn('estado', ['programada', 'confirmada']);
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true)->orderBy('orden');
    }

    public function getNombreCompletoAttribute(): string
    {
        return $this->area->secretaria->nombre . ' - ' . $this->area->nombre . ' - ' . $this->nombre;
    }

    public function getCostoFormateadoAttribute(): string
    {
        return $this->es_gratuito ? 'Gratuito' : '$' . number_format($this->costo, 0, ',', '.');
    }

    // Crear configuración por defecto al crear el trámite
    protected static function booted()
    {
        static::created(function ($tramite) {
            $tramite->configuracion()->create([
                'hora_inicio' => '08:00:00',
                'hora_fin' => '17:00:00',
                'dias_disponibles' => ['1', '2', '3', '4', '5'],
                'citas_por_hora' => 4,
                'dias_anticipacion_minima' => 1,
                'dias_anticipacion_maxima' => 30,
            ]);
        });
    }
}