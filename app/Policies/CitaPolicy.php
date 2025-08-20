<?php
// app/Policies/CitaPolicy.php

namespace App\Policies;

use App\Models\Cita;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CitaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_all_citas') || $user->hasPermission('view_area_citas');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Cita $cita): bool
    {
        // Super admin puede ver todo
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Si puede ver todas las citas
        if ($user->hasPermission('view_all_citas')) {
            return true;
        }

        // Si puede ver citas de su área y la cita pertenece a su área
        if ($user->hasPermission('view_area_citas') && $user->area_id) {
            return $cita->tramite->area_id === $user->area_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('manage_citas');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Cita $cita): bool
    {
        // Super admin puede editar todo
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Si no tiene permiso para gestionar citas
        if (!$user->hasPermission('manage_citas')) {
            return false;
        }

        // Si puede gestionar todas las citas
        if ($user->hasPermission('view_all_citas')) {
            return true;
        }

        // Si puede gestionar citas de su área y la cita pertenece a su área
        if ($user->area_id) {
            return $cita->tramite->area_id === $user->area_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Cita $cita): bool
    {
        // Solo super admin puede eliminar citas
        return $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Cita $cita): bool
    {
        return $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Cita $cita): bool
    {
        return $user->hasRole('super_admin');
    }
}