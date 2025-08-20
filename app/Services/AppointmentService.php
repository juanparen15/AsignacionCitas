<?php

// app/Services/AppointmentService.php
namespace App\Services;

use App\Models\Appointment;
use App\Models\Service;
use App\Mail\AppointmentConfirmation;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AppointmentService
{
    public function createAppointment(Service $service, array $data): Appointment
    {
        return DB::transaction(function () use ($service, $data) {
            // Verificar disponibilidad una vez más por seguridad
            $this->validateAvailability($service, $data['appointment_date']);

            $appointment = Appointment::create([
                'service_id' => $service->id,
                'client_name' => $data['client_name'],
                'client_email' => $data['client_email'],
                'client_phone' => $data['client_phone'] ?? null,
                'appointment_date' => $data['appointment_date'],
                'notes' => $data['notes'] ?? null,
                'price' => $service->price,
                'status' => config('appointments.email.auto_confirm_appointments', false) ? 'confirmed' : 'pending',
            ]);

            // Enviar email de confirmación
            if (config('appointments.email.send_confirmation', true)) {
                try {
                    Mail::to($appointment->client_email)
                        ->send(new AppointmentConfirmation($appointment));
                } catch (\Exception $e) {
                    // Log el error pero no fallar la creación de la cita
                    Log::error('Failed to send appointment confirmation email: ' . $e->getMessage());
                }
            }

            return $appointment;
        });
    }

    public function getAvailableSlots(Service $service, string $date): array
    {
        $date = Carbon::parse($date);
        $dayOfWeek = $date->dayOfWeek === 0 ? 7 : $date->dayOfWeek;

        // Verificar si el día está disponible
        if (!in_array($dayOfWeek, $service->availability_days ?? [])) {
            return [];
        }

        // Verificar que no sea una fecha pasada
        if ($date->lt(Carbon::today())) {
            return [];
        }

        // Verificar que esté dentro del rango de reserva anticipada
        $maxDate = Carbon::today()->addDays($service->advance_booking_days);
        if ($date->gt($maxDate)) {
            return [];
        }

        $slots = [];
        $startTime = $date->copy()->setTimeFromTimeString($service->start_time);
        $endTime = $date->copy()->setTimeFromTimeString($service->end_time);

        while ($startTime->lt($endTime)) {
            $slotEnd = $startTime->copy()->addMinutes($service->duration);

            if ($slotEnd->lte($endTime)) {
                // Verificar disponibilidad
                if ($this->isSlotAvailable($service, $startTime, $slotEnd)) {
                    $slots[] = [
                        'start' => $startTime->format('H:i'),
                        'end' => $slotEnd->format('H:i'),
                        'datetime' => $startTime->toDateTimeString(),
                        'available' => true,
                    ];
                }
            }

            $startTime->addMinutes($service->slot_interval);
        }

        return $slots;
    }

    private function validateAvailability(Service $service, string $appointmentDate): void
    {
        $date = Carbon::parse($appointmentDate);
        $slots = $this->getAvailableSlots($service, $date->format('Y-m-d'));
        $requestedTime = $date->format('H:i');

        $isAvailable = collect($slots)->contains('start', $requestedTime);

        if (!$isAvailable) {
            throw new \InvalidArgumentException('La hora seleccionada no está disponible.');
        }
    }

    private function isSlotAvailable(Service $service, Carbon $startTime, Carbon $endTime): bool
    {
        // Verificar citas existentes
        $existingAppointments = Appointment::where('service_id', $service->id)
            ->where('appointment_date', '>=', $startTime)
            ->where('appointment_date', '<', $endTime)
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        if ($existingAppointments >= $service->max_bookings_per_slot) {
            return false;
        }

        // Verificar tiempos bloqueados
        $blockedTimes = $service->blockedTimes()
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where('start_time', '<=', $startTime)
                    ->where('end_time', '>', $startTime);
            })
            ->orWhere(function ($query) use ($startTime, $endTime) {
                $query->where('start_time', '<', $endTime)
                    ->where('end_time', '>=', $endTime);
            })
            ->exists();

        return !$blockedTimes;
    }

    public function cancelAppointment(Appointment $appointment, string $reason = null): bool
    {
        if (!$appointment->canBeCancelled()) {
            return false;
        }

        $appointment->cancel($reason);

        // Aquí podrías enviar un email de cancelación
        // Mail::to($appointment->client_email)->send(new AppointmentCancelled($appointment));

        return true;
    }

    public function generateCancelToken(Appointment $appointment): string
    {
        return hash('sha256', $appointment->id . $appointment->client_email . config('app.key'));
    }

    public function verifyCancelToken(Appointment $appointment, string $token): bool
    {
        $expectedToken = $this->generateCancelToken($appointment);
        return hash_equals($expectedToken, $token);
    }
}
