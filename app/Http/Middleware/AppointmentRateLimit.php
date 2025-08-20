<?php

// namespace App\Http\Middleware;

// use Closure;
// use Illuminate\Http\Request;
// use Symfony\Component\HttpFoundation\Response;

// class AppointmentRateLimit
// {
//     /**
//      * Handle an incoming request.
//      *
//      * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
//      */
//     public function handle(Request $request, Closure $next): Response
//     {
//         return $next($request);
//     }
// }


// app/Http/Middleware/AppointmentRateLimit.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class AppointmentRateLimit
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!config('appointments.security.enable_rate_limiting')) {
            return $next($request);
        }

        $key = 'appointment_booking:' . $request->ip();
        $maxAttempts = config('appointments.security.max_attempts_per_hour', 10);

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            
            return response()->json([
                'message' => 'Demasiados intentos de reserva. Intenta nuevamente en ' . ceil($seconds / 60) . ' minutos.',
                'retry_after' => $seconds
            ], 429);
        }

        RateLimiter::hit($key, 3600); // 1 hora

        return $next($request);
    }
}