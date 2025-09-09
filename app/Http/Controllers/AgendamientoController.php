<?php
// app/Http/Controllers/AgendamientoController.php

namespace App\Http\Controllers;

use App\Models\Secretaria;
use App\Models\Area;
use App\Models\Tramite;
use App\Models\Cita;
use App\Models\ConfiguracionTramite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class AgendamientoController extends Controller
{
    public function index()
    {
        $secretarias = Secretaria::activas()
            ->with(['areasActivas.tramitesActivos'])
            ->get();

        return view('agendamiento.index', compact('secretarias'));
    }

    public function getAreas(Request $request, $secretariaId)
    {
        $areas = Area::where('secretaria_id', $secretariaId)
            ->where('activa', true)
            ->with(['tramitesActivos'])
            ->orderBy('orden')
            ->get();

        return response()->json($areas);
    }

    public function getTramites(Request $request, $areaId)
    {
        $tramites = Tramite::where('area_id', $areaId)
            ->where('activo', true)
            ->with(['configuracion'])
            ->orderBy('orden')
            ->get();

        return response()->json($tramites);
    }

    public function getConfiguracionTramite(Request $request, $tramiteId)
    {
        $tramite = Tramite::with(['configuracion', 'area.secretaria'])
            ->findOrFail($tramiteId);

        if (!$tramite->configuracion) {
            return response()->json(['error' => 'Trámite sin configuración'], 400);
        }

        $configuracion = $tramite->configuracion;

        return response()->json([
            'tramite' => $tramite,
            'configuracion' => $configuracion,
            'fecha_minima' => $configuracion->fecha_minima_cita->format('Y-m-d'),
            'fecha_maxima' => $configuracion->fecha_maxima_cita->format('Y-m-d'),
        ]);
    }

    public function getFechasDisponibles(Request $request, $tramiteId)
    {
        $tramite = Tramite::with('configuracion')->findOrFail($tramiteId);
        $configuracion = $tramite->configuracion;

        if (!$configuracion) {
            return response()->json(['error' => 'Trámite sin configuración'], 400);
        }

        $fechaInicio = $configuracion->fecha_minima_cita;
        $fechaFin = $configuracion->fecha_maxima_cita;
        $fechasDisponibles = [];

        $fecha = $fechaInicio->copy();
        while ($fecha->lte($fechaFin)) {
            // Verificar si es día disponible y no es inhábil
            if ($configuracion->isDiaDisponible($fecha) && !$configuracion->isDiaInhabil($fecha)) {
                $fechasDisponibles[] = $fecha->format('Y-m-d');
            }
            $fecha->addDay();
        }

        return response()->json($fechasDisponibles);
    }

    public function getHorasDisponibles(Request $request, $tramiteId, $fecha)
    {
        $tramite = Tramite::with('configuracion')->findOrFail($tramiteId);
        $configuracion = $tramite->configuracion;

        if (!$configuracion) {
            return response()->json(['error' => 'Trámite sin configuración'], 400);
        }

        $fechaCarbon = Carbon::parse($fecha);

        // Verificar que la fecha sea válida
        if (!$configuracion->isDiaDisponible($fechaCarbon) || $configuracion->isDiaInhabil($fechaCarbon)) {
            return response()->json([
                'error' => 'Fecha no disponible',
                'horas' => [],
                'mensaje' => 'La fecha seleccionada no está disponible'
            ], 400);
        }

        // Obtener horas disponibles con la nueva validación
        $horasDisponibles = $configuracion->getHorasDisponibles($fechaCarbon);
        $horasConDisponibilidad = [];

        foreach ($horasDisponibles as $hora) {
            // Contar cuántas citas ya hay en esa hora
            $citasEnHora = Cita::where('tramite_id', $tramiteId)
                ->whereDate('fecha_cita', $fecha)
                ->whereTime('hora_cita', $hora)
                ->whereIn('estado', ['programada', 'confirmada'])
                ->count();

            $disponibles = $configuracion->citas_por_hora - $citasEnHora;

            if ($disponibles > 0) {
                $horasConDisponibilidad[] = [
                    'hora' => $hora,
                    'disponibles' => $disponibles,
                    'total' => $configuracion->citas_por_hora,
                ];
            }
        }

        // Obtener mensaje explicativo
        $mensaje = $configuracion->getMensajeDisponibilidad($fechaCarbon);

        // Información adicional para el frontend
        $info = [
            'es_hoy' => $fechaCarbon->isToday(),
            'fecha_formateada' => $fechaCarbon->format('d/m/Y'),
            'hora_actual' => now()->format('H:i'),
            'horario_almuerzo' => ConfiguracionTramite::getHorarioAlmuerzo()
        ];

        if ($fechaCarbon->isToday()) {
            $info['hora_minima_hoy'] = $configuracion->getHoraMinimaHoy();
        }

        return response()->json([
            'horas' => $horasConDisponibilidad,
            'mensaje' => $mensaje,
            'info' => $info,
            'total_disponibles' => count($horasConDisponibilidad)
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tramite_id' => 'required|exists:tramites,id',
            'fecha_cita' => 'required|date|after_or_equal:today|before_or_equal:' . now()->addMonths(2)->format('Y-m-d'),
            'hora_cita' => 'required|date_format:H:i',
            'tipo_documento' => 'required|in:CC,CE,PA',
            'numero_documento' => 'required|string|max:20|regex:/^[0-9]+$/',
            'nombres' => 'required|string|max:100|regex:/^[a-zA-ZÀ-ÿ\s]+$/',
            'apellidos' => 'required|string|max:100|regex:/^[a-zA-ZÀ-ÿ\s]+$/',
            'email' => 'required|email|max:150',
            'telefono' => 'required|string|max:15|regex:/^[0-9+\-\s()]+$/',
            'direccion' => 'nullable|string|max:200',
            'acepta_tratamiento_datos' => 'required|accepted',
        ], [
            'tramite_id.required' => 'Debe seleccionar un trámite',
            'tramite_id.exists' => 'El trámite seleccionado no es válido',
            'fecha_cita.required' => 'Debe seleccionar una fecha',
            'fecha_cita.date' => 'La fecha debe ser válida',
            'fecha_cita.after_or_equal' => 'La fecha no puede ser anterior a hoy',
            'fecha_cita.before_or_equal' => 'La fecha no puede ser posterior a 2 meses',
            'hora_cita.required' => 'Debe seleccionar una hora',
            'hora_cita.date_format' => 'La hora debe tener formato válido (HH:MM)',
            'tipo_documento.required' => 'Debe seleccionar el tipo de documento',
            'numero_documento.required' => 'El número de documento es obligatorio',
            'numero_documento.regex' => 'El número de documento solo puede contener números',
            'nombres.required' => 'Los nombres son obligatorios',
            'nombres.regex' => 'Los nombres solo pueden contener letras y espacios',
            'apellidos.required' => 'Los apellidos son obligatorios',
            'apellidos.regex' => 'Los apellidos solo pueden contener letras y espacios',
            'email.required' => 'El correo electrónico es obligatorio',
            'email.email' => 'Debe ingresar un correo electrónico válido',
            'telefono.required' => 'El teléfono es obligatorio',
            'telefono.regex' => 'El teléfono solo puede contener números, espacios, +, - y paréntesis',
            'acepta_tratamiento_datos.accepted' => 'Debe aceptar el tratamiento de datos personales',
        ]);

        if ($validator->fails()) {
            Log::warning('Validación falló al agendar cita', [
                'errors' => $validator->errors()->toArray(),
                'data' => $request->except(['acepta_tratamiento_datos'])
            ]);

            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Log de datos recibidos
            Log::info('Iniciando proceso de creación de cita', [
                'tramite_id' => $request->tramite_id,
                'fecha_cita' => $request->fecha_cita,
                'hora_cita' => $request->hora_cita,
                'documento' => $request->tipo_documento . '-' . $request->numero_documento
            ]);

            // Verificar disponibilidad nuevamente
            $tramite = Tramite::with('configuracion')->findOrFail($request->tramite_id);
            $configuracion = $tramite->configuracion;

            if (!$configuracion) {
                Log::error('Trámite sin configuración', ['tramite_id' => $request->tramite_id]);
                return response()->json([
                    'success' => false,
                    'message' => 'El trámite seleccionado no tiene configuración disponible'
                ], 400);
            }

            $citasEnHora = Cita::where('tramite_id', $request->tramite_id)
                ->whereDate('fecha_cita', $request->fecha_cita)
                ->whereTime('hora_cita', $request->hora_cita)
                ->whereIn('estado', ['programada', 'confirmada'])
                ->count();

            if ($citasEnHora >= $configuracion->citas_por_hora) {
                Log::warning('No hay disponibilidad para la hora solicitada', [
                    'tramite_id' => $request->tramite_id,
                    'fecha_cita' => $request->fecha_cita,
                    'hora_cita' => $request->hora_cita,
                    'citas_existentes' => $citasEnHora,
                    'limite' => $configuracion->citas_por_hora
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Ya no hay disponibilidad para esta fecha y hora'
                ], 400);
            }

            // Verificar si ya existe una cita para esta persona en la misma fecha
            $citaExistente = Cita::where('tipo_documento', $request->tipo_documento)
                ->where('numero_documento', $request->numero_documento)
                ->whereDate('fecha_cita', $request->fecha_cita)
                ->whereIn('estado', ['programada', 'confirmada'])
                ->first();

            if ($citaExistente) {
                Log::warning('Usuario ya tiene cita programada', [
                    'documento' => $request->tipo_documento . '-' . $request->numero_documento,
                    'fecha_cita' => $request->fecha_cita,
                    'cita_existente' => $citaExistente->numero_cita
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Ya tiene una cita programada para esta fecha'
                ], 400);
            }

            // Preparar datos para crear la cita
            $datosCita = [
                'tramite_id' => $request->tramite_id,
                'fecha_cita' => $request->fecha_cita,
                'hora_cita' => $request->hora_cita,
                'tipo_documento' => $request->tipo_documento,
                'numero_documento' => $request->numero_documento,
                'nombres' => strtoupper($request->nombres),
                'apellidos' => strtoupper($request->apellidos),
                'email' => strtolower($request->email),
                'telefono' => $request->telefono,
                'direccion' => $request->direccion,
                'acepta_tratamiento_datos' => $request->acepta_tratamiento_datos,
                'estado' => 'programada',
            ];

            Log::info('Creando cita con datos', $datosCita);

            // Crear la cita
            $cita = Cita::create($datosCita);

            Log::info('Cita creada exitosamente', [
                'cita_id' => $cita->id,
                'numero_cita' => $cita->numero_cita
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cita agendada exitosamente',
                'cita' => $cita->load(['tramite.area.secretaria']),
                'numero_cita' => $cita->numero_cita
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al crear cita', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->except(['acepta_tratamiento_datos'])
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al agendar la cita. Por favor intente nuevamente.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function generarPDF($numeroCita)
    {
        $cita = Cita::with(['tramite.area.secretaria'])
            ->where('numero_cita', $numeroCita)
            ->firstOrFail();

        $pdf = Pdf::loadView('agendamiento.comprobante-pdf', compact('cita'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("comprobante-cita-{$numeroCita}.pdf");
    }

    public function consultarCita(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'numero_cita' => 'required|string',
            'numero_documento' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $cita = Cita::with(['tramite.area.secretaria'])
            ->where('numero_cita', $request->numero_cita)
            ->where('numero_documento', $request->numero_documento)
            ->first();

        if (!$cita) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró ninguna cita con los datos proporcionados'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'cita' => $cita
        ]);
    }
}
