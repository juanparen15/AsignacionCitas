<?php
// app/Http/Middleware/ValidarDisponibilidadCita.php - Versión mejorada

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
                $ahora = now();
                
                // NUEVA VALIDACIÓN: Verificar que no sea una hora pasada si es hoy
                if ($fecha->isToday()) {
                    $horaEspecificaHoy = Carbon::today()->setTimeFromTimeString($horaCita);
                    
                    // Verificar que no sea una hora que ya pasó
                    if ($horaEspecificaHoy->lte($ahora)) {
                        return response()->json([
                            'success' => false,
                            'message' => "No puede agendar una cita en una hora que ya pasó ({$horaCita}). La hora actual es {$ahora->format('H:i')}."
                        ], 400);
                    }
                    
                    // Verificar anticipación mínima de 1 hora
                    if ($horaEspecificaHoy->lte($ahora->copy()->addHour())) {
                        $horaMinima = $ahora->copy()->addHour()->ceilMinute(60)->format('H:i');
                        return response()->json([
                            'success' => false,
                            'message' => "Para citas del mismo día, debe agendar con al menos 1 hora de anticipación. Hora mínima disponible: {$horaMinima}."
                        ], 400);
                    }
                }
                
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
                
                // MEJORADA: Usar el nuevo método de validación
                if (!$configuracion->isHoraDisponibleParaFecha($horaCita, $fecha)) {
                    $horarioAlmuerzo = ConfiguracionTramite::getHorarioAlmuerzo();
                    
                    $mensaje = "La hora seleccionada ({$horaCita}) no está disponible.";
                    
                    if ($fecha->isToday()) {
                        $horaMinima = $configuracion->getHoraMinimaHoy();
                        if ($horaMinima) {
                            $mensaje .= " Para hoy, las horas disponibles son desde las {$horaMinima}.";
                        } else {
                            $mensaje .= " No hay más horarios disponibles para hoy.";
                        }
                    } else {
                        $mensaje .= " Horario de almuerzo: {$horarioAlmuerzo['inicio']} - {$horarioAlmuerzo['fin']}.";
                    }
                    
                    return response()->json([
                        'success' => false,
                        'message' => $mensaje
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
                
                // Validar disponibilidad de cupos en esa hora
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