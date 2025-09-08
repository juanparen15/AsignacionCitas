<?php
// app/Http/Middleware/ValidarDisponibilidadCita.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Tramite;
use App\Models\Cita;
use App\Models\ConfiguracionTramite;
use Carbon\Carbon;

class ValidarDisponibilidadCita
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('post') && $request->routeIs('agendamiento.store')) {
            $tramiteId = $request->input('tramite_id');
            $fechaCita = $request->input('fecha_cita');
            $horaCita = $request->input('hora_cita');
            
            if ($tramiteId && $fechaCita && $horaCita) {
                $tramite = Tramite::with('configuracion')->find($tramiteId);
                
                if (!$tramite || !$tramite->configuracion) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Trámite no válido o sin configuración'
                    ], 400);
                }
                
                $configuracion = $tramite->configuracion;
                $fecha = Carbon::parse($fechaCita);
                
                // Validar que la fecha esté dentro del rango permitido
                if ($fecha->lt($configuracion->fecha_minima_cita) || 
                    $fecha->gt($configuracion->fecha_maxima_cita)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'La fecha seleccionada no está disponible'
                    ], 400);
                }
                
                // Validar que sea un día disponible
                if (!$configuracion->isDiaDisponible($fecha)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'El día seleccionado no está disponible para este trámite'
                    ], 400);
                }
                
                // Validar que no sea día inhábil
                if ($configuracion->isDiaInhabil($fecha)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'La fecha seleccionada es un día inhábil'
                    ], 400);
                }
                
                // NUEVA VALIDACIÓN: Verificar que no sea horario de almuerzo
                if (!$configuracion->isHoraDisponible($horaCita)) {
                    $horarioAlmuerzo = ConfiguracionTramite::getHorarioAlmuerzo();
                    return response()->json([
                        'success' => false,
                        'message' => "La hora seleccionada ({$horaCita}) no está disponible. " . 
                                   "Horario de almuerzo: {$horarioAlmuerzo['inicio']} - {$horarioAlmuerzo['fin']}"
                    ], 400);
                }
                
                // Validar que la hora esté dentro del horario de atención
                $horaInicio = Carbon::parse($configuracion->hora_inicio)->format('H:i');
                $horaFin = Carbon::parse($configuracion->hora_fin)->format('H:i');
                
                if ($horaCita < $horaInicio || $horaCita >= $horaFin) {
                    return response()->json([
                        'success' => false,
                        'message' => "La hora debe estar entre {$horaInicio} y {$horaFin}"
                    ], 400);
                }
                
                // Validar disponibilidad de hora
                $citasEnHora = Cita::where('tramite_id', $tramiteId)
                    ->whereDate('fecha_cita', $fechaCita)
                    ->whereTime('hora_cita', $horaCita)
                    ->whereIn('estado', ['programada', 'confirmada'])
                    ->count();
                    
                if ($citasEnHora >= $configuracion->citas_por_hora) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Ya no hay disponibilidad para esta fecha y hora'
                    ], 400);
                }
            }
        }
        
        return $next($request);
    }
}