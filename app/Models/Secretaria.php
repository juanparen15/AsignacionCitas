<?php
// app/Models/Secretaria.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Secretaria extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'activa',
        'orden',
    ];

    protected $casts = [
        'activa' => 'boolean',
        'orden' => 'integer',
    ];

    public function areas(): HasMany
    {
        return $this->hasMany(Area::class)->orderBy('orden');
    }

    public function areasActivas(): HasMany
    {
        return $this->areas()->where('activa', true);
    }

    public function scopeActivas($query)
    {
        return $query->where('activa', true)->orderBy('orden');
    }

    public function scopeActive($query)
    {
        return $query->where('activa', true)->orderBy('orden');
    }
}