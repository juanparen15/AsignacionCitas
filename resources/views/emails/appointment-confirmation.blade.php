{{-- // resources/views/emails/appointment-confirmation.blade.php --}}
@component('mail::message')
    # Confirmación de Cita

    Hola {{ $appointment->client_name }},

    Tu cita ha sido reservada exitosamente. Aquí están los detalles:

    @component('mail::panel')
        **Servicio:** {{ $appointment->service->name }}
        **Fecha y Hora:** {{ $appointment->appointment_date->format('d/m/Y H:i') }}
        **Cliente:** {{ $appointment->client_name }}
        **Email:** {{ $appointment->client_email }}
        @if ($appointment->client_phone)
            **Teléfono:** {{ $appointment->client_phone }}
        @endif
        @if ($appointment->price > 0)
            **Precio:** ${{ number_format($appointment->price, 0, ',', '.') }}
        @endif
        **Estado:** Pendiente de confirmación
    @endcomponent

    @if ($appointment->notes)
        **Notas adicionales:**
        {{ $appointment->notes }}
    @endif

    ## Importante:
    - Por favor llega 10 minutos antes de tu cita
    - Si necesitas cancelar, usa el enlace al final de este email
    - Te confirmaremos la cita pronto

    @component('mail::button', ['url' => config('app.url')])
        Ver Mi Cita
    @endcomponent

    Si necesitas cancelar tu cita, puedes hacerlo haciendo clic en el siguiente enlace:

    @component('mail::button', ['url' => $cancelUrl, 'color' => 'red'])
        Cancelar Cita
    @endcomponent

    Gracias por confiar en nosotros,
    {{ config('app.name') }}

    @component('mail::subcopy')
        Si tienes problemas haciendo clic en los botones, copia y pega la siguiente URL en tu navegador: {{ $cancelUrl }}
    @endcomponent
@endcomponent
