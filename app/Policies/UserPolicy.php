<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_users') || $user->hasPermission('manage_users');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Super admin puede ver todo
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Los usuarios pueden ver su propio perfil
        if ($user->id === $model->id) {
            return true;
        }

        return $user->hasPermission('view_users') || $user->hasPermission('manage_users');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('manage_users');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Super admin puede editar todo
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Los usuarios pueden editar su propio perfil (campos limitados)
        if ($user->id === $model->id) {
            return true;
        }

        // No se puede editar a un super admin a menos que seas super admin
        if ($model->hasRole('super_admin') && !$user->hasRole('super_admin')) {
            return false;
        }

        return $user->hasPermission('manage_users');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // No se puede borrar a sí mismo
        if ($user->id === $model->id) {
            return false;
        }

        // Solo super admin puede borrar super admins
        if ($model->hasRole('super_admin') && !$user->hasRole('super_admin')) {
            return false;
        }

        return $user->hasPermission('manage_users');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->hasPermission('manage_users');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->hasRole('super_admin');
    }
}