<?php

// app/Http/Controllers/AppointmentController.php
namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Appointment;
use App\Http\Requests\StoreAppointmentRequest;
use App\Services\AppointmentService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class AppointmentController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    
    protected $appointmentService;

    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
        
        // Aplicar rate limiting a las rutas de reserva
        $this->middleware('throttle:appointments')->only(['store']);
    }

    public function index()
    {
        $services = Service::where('is_active', true)
            ->orderBy('name')
            ->get();
            
        return view('appointments.index', compact('services'));
    }

    public function show(Service $service)
    {
        if (!$service->is_active) {
            abort(404, 'Servicio no disponible');
        }

        return view('appointments.show', compact('service'));
    }

    public function getAvailableSlots(Request $request, Service $service)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today'
        ]);

        if (!$service->is_active) {
            return response()->json(['error' => 'Servicio no disponible'], 404);
        }

        $date = $request->get('date');
        $slots = $this->appointmentService->getAvailableSlots($service, $date);

        return response()->json([
            'slots' => $slots,
            'date' => $date,
            'service' => $service->name
        ]);
    }

    public function store(StoreAppointmentRequest $request, Service $service)
    {
        if (!$service->is_active) {
            return back()->withErrors(['service' => 'Este servicio no está disponible.']);
        }

        try {
            $appointment = $this->appointmentService->createAppointment(
                $service,
                $request->validated()
            );

            return redirect()->route('appointments.success', $appointment->id)
                ->with('success', '¡Tu cita ha sido reservada exitosamente!');

        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['appointment_date' => $e->getMessage()]);
        } catch (\Exception $e) {
            Log::error('Error creating appointment: ' . $e->getMessage());
            return back()->withErrors(['general' => 'Ocurrió un error al procesar tu reserva. Por favor intenta nuevamente.']);
        }
    }

    public function success(Appointment $appointment)
    {
        return view('appointments.success', compact('appointment'));
    }

    public function cancel($id, $token)
    {
        $appointment = Appointment::where('id', $id)
            ->where('status', '!=', 'cancelled')
            ->firstOrFail();

        if (!$this->appointmentService->verifyCancelToken($appointment, $token)) {
            abort(403, 'Token de cancelación inválido');
        }

        // Solo permitir cancelación si falta más de 2 horas
        if ($appointment->appointment_date->diffInHours(now()) < 2) {
            return view('appointments.cancel-error', [
                'appointment' => $appointment,
                'message' => 'No puedes cancelar una cita con menos de 2 horas de anticipación. Por favor contacta directamente.'
            ]);
        }

        $this->appointmentService->cancelAppointment($appointment, 'Cancelado por el cliente');

        return view('appointments.cancelled', compact('appointment'));
    }

    public function myAppointments(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $appointments = Appointment::with('service')
            ->where('client_email', $request->email)
            ->orderBy('appointment_date', 'desc')
            ->paginate(10);

        return view('appointments.my-appointments', compact('appointments'));
    }
}