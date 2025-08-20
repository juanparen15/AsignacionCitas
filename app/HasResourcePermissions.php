<?php
// app/Traits/HasResourcePermissions.php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait HasResourcePermissions
{
    protected static function getRequiredPermission(): string
    {
        // Este método debe ser implementado en cada Resource
        throw new \Exception('El método getRequiredPermission() debe ser implementado en ' . static::class);
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->hasPermission(static::getRequiredPermission()) || 
               Auth::user()->hasRole('super_admin');
    }

    public static function canCreate(): bool
    {
        return Auth::user()->hasPermission(static::getRequiredPermission()) || 
               Auth::user()->hasRole('super_admin');
    }

    public static function canEdit(Model $record): bool
    {
        return Auth::user()->hasPermission(static::getRequiredPermission()) || 
               Auth::user()->hasRole('super_admin');
    }

    public static function canDelete(Model $record): bool
    {
        return Auth::user()->hasPermission(static::getRequiredPermission()) || 
               Auth::user()->hasRole('super_admin');
    }

    public static function canDeleteAny(): bool
    {
        return Auth::user()->hasPermission(static::getRequiredPermission()) || 
               Auth::user()->hasRole('super_admin');
    }
}