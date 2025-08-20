<?php

// app/Observers/AppointmentObserver.php
namespace App\Observers;

use App\Models\Appointment;
use App\Mail\AppointmentStatusChanged;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AppointmentObserver
{
    public function updated(Appointment $appointment): void
    {
        // Si el estado cambió, enviar notificación
        if ($appointment->isDirty('status')) {
            $originalStatus = $appointment->getOriginal('status');
            $newStatus = $appointment->status;

            // Enviar email cuando se confirma una cita
            if ($newStatus === 'confirmed' && $originalStatus === 'pending') {
                try {
                    // Mail::to($appointment->client_email)->send(new AppointmentConfirmed($appointment));
                } catch (\Exception $e) {
                    Log::error('Failed to send appointment confirmed email: ' . $e->getMessage());
                }
            }

            // Log del cambio de estado
            Log::info("Appointment {$appointment->id} status changed from {$originalStatus} to {$newStatus}");
        }
    }
}