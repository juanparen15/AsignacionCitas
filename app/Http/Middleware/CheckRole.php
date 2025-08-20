<?php
// app/Http/Middleware/CheckRole.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('filament.admin.auth.login');
        }

        $user = Auth::user();

        // Super admin tiene acceso a todo
        if ($user->hasRole('super_admin')) {
            return $next($request);
        }

        // Verificar si el usuario tiene alguno de los roles especificados
        if (!$user->hasAnyRole($roles)) {
            abort(403, 'No tienes el rol necesario para acceder a esta sección.');
        }

        return $next($request);
    }
}