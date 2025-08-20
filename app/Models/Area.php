<?php
// app/Models/Area.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    use HasFactory;

    protected $fillable = [
        'secretaria_id',
        'nombre',
        'descripcion',
        'activa',
        'orden',
    ];

    protected $casts = [
        'activa' => 'boolean',
        'orden' => 'integer',
    ];

    public function secretaria(): BelongsTo
    {
        return $this->belongsTo(Secretaria::class);
    }

    public function tramites(): HasMany
    {
        return $this->hasMany(Tramite::class)->orderBy('orden');
    }

    public function tramitesActivos(): HasMany
    {
        return $this->tramites()->where('activo', true);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function scopeActivas($query)
    {
        return $query->where('activa', true)->orderBy('orden');
    }

    public function scopeActive($query)
    {
        return $query->where('activa', true)->orderBy('orden');
    }

    public function getNombreCompletoAttribute(): string
    {
        return $this->secretaria->nombre . ' - ' . $this->nombre;
    }
}