{{-- // resources/views/emails/appointment-reminder.blade.php --}}
@component('mail::message')
    # Recordatorio de Cita

    Hola {{ $appointment->client_name }},

    Te recordamos que tienes una cita programada para mañana:

    @component('mail::panel')
        **Servicio:** {{ $appointment->service->name }}
        **Fecha y Hora:** {{ $appointment->appointment_date->format('d/m/Y H:i') }}
        **Cliente:** {{ $appointment->client_name }}
        @if ($appointment->client_phone)
            **Teléfono:** {{ $appointment->client_phone }}
        @endif
        @if ($appointment->price > 0)
            **Precio:** ${{ number_format($appointment->price, 0, ',', '.') }}
        @endif
    @endcomponent

    ## Recordatorios importantes:
    - Llega 10 minutos antes de tu cita
    - Trae un documento de identidad
    - Si tienes alguna condición especial, infórmanos

    @if ($appointment->notes)
        **Notas de tu reserva:**
        {{ $appointment->notes }}
    @endif

    Si no puedes asistir, por favor cancela tu cita lo antes posible:

    @component('mail::button', ['url' => $cancelUrl, 'color' => 'red'])
        Cancelar Cita
    @endcomponent

    ¡Te esperamos!
    {{ config('app.name') }}

    @component('mail::subcopy')
        Si tienes problemas haciendo clic en el botón, copia y pega la siguiente URL en tu navegador: {{ $cancelUrl }}
    @endcomponent
@endcomponent
