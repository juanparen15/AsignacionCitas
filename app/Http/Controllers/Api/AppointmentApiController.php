<?php

// app/Http/Controllers/Api/AppointmentApiController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AppointmentApiController extends Controller
{
    public function services()
    {
        $services = Service::where('is_active', true)
            ->select('id', 'name', 'description', 'price', 'duration')
            ->get();

        return response()->json($services);
    }

    public function availableSlots(Request $request, Service $service)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today'
        ]);

        $date = $request->get('date');
        $slots = $service->getAvailableSlots($date);

        return response()->json([
            'success' => true,
            'data' => [
                'service' => $service->name,
                'date' => $date,
                'slots' => $slots
            ]
        ]);
    }

    public function book(Request $request, Service $service)
    {
        $request->validate([
            'client_name' => 'required|string|max:255',
            'client_email' => 'required|email|max:255',
            'client_phone' => 'nullable|string|max:255',
            'appointment_date' => [
                'required',
                'date',
                'after:now',
                Rule::unique('appointments')->where(function ($query) use ($service, $request) {
                    return $query->where('service_id', $service->id)
                                ->where('appointment_date', $request->appointment_date)
                                ->whereIn('status', ['pending', 'confirmed']);
                })
            ],
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $appointment = Appointment::create([
                'service_id' => $service->id,
                'client_name' => $request->client_name,
                'client_email' => $request->client_email,
                'client_phone' => $request->client_phone,
                'appointment_date' => $request->appointment_date,
                'notes' => $request->notes,
                'price' => $service->price,
                'status' => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cita reservada exitosamente',
                'data' => [
                    'appointment_id' => $appointment->id,
                    'appointment_date' => $appointment->appointment_date,
                    'service' => $service->name,
                    'status' => $appointment->status
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al reservar la cita',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function myAppointments(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $appointments = Appointment::with('service')
            ->where('client_email', $request->email)
            ->orderBy('appointment_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $appointments
        ]);
    }
}