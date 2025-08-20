<?php
// app/Providers/AppServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Cita;
use App\Policies\UserPolicy;
use App\Policies\CitaPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configurar Carbon en español
        Carbon::setLocale('es');
        setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'Spanish_Spain', 'Spanish');
        
        // Configurar fechas en español para Carbon
        Carbon::macro('toSpanishDate', function () {
            $months = [
                1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
                5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
                9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
            ];
            
            $days = [
                0 => 'domingo', 1 => 'lunes', 2 => 'martes', 3 => 'miércoles',
                4 => 'jueves', 5 => 'viernes', 6 => 'sábado'
            ];
            
            return $days[$this->dayOfWeek] . ', ' . $this->day . ' de ' . $months[$this->month] . ' de ' . $this->year;
        });

        // Registrar policies
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Cita::class, CitaPolicy::class);

        // Gates personalizados
        Gate::define('access-admin-panel', function (User $user) {
            return $user->active && $user->hasVerifiedEmail();
        });

        Gate::define('manage-system-settings', function (User $user) {
            return $user->hasPermission('system_settings') || $user->hasRole('super_admin');
        });

        Gate::define('view-sensitive-data', function (User $user) {
            return $user->hasRole('super_admin') || $user->hasPermission('view_logs');
        });
    }
}