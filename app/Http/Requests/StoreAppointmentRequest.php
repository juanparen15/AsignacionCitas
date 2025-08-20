<?php

// app/Http/Requests/StoreAppointmentRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class StoreAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'client_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-ZÀ-ÿ\u00f1\u00d1\s]+$/' // Solo letras y espacios
            ],
            'client_document' => [
                'required',
                'string',
                'max:20',
                'regex:/^[0-9]+$/', // Solo números
                'unique:appointments,client_document,NULL,id,appointment_date,' . $this->appointment_date . ',status,pending'
            ],
            'client_email' => [
                'required',
                'email',
                'max:255'
            ],
            'client_phone' => [
                'required',
                'string',
                'max:20',
                'regex:/^[0-9+\-\s()]+$/' // Números, espacios, +, -, ()
            ],
            'appointment_date' => [
                'required',
                'date',
                'after_or_equal:today',
                'before_or_equal:' . now()->addDays(60)->toDateString() // Máximo 60 días adelante
            ],
            'appointment_time' => [
                'required',
                'date_format:H:i'
            ],
            'notes' => [
                'nullable',
                'string',
                'max:500'
            ],
            'terms_accepted' => [
                'required',
                'accepted'
            ]
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'client_name.required' => 'El nombre es obligatorio.',
            'client_name.regex' => 'El nombre solo puede contener letras y espacios.',
            'client_document.required' => 'El documento de identidad es obligatorio.',
            'client_document.regex' => 'El documento debe contener solo números.',
            'client_document.unique' => 'Ya existe una cita pendiente con este documento para la fecha seleccionada.',
            'client_email.required' => 'El correo electrónico es obligatorio.',
            'client_email.email' => 'El correo electrónico debe tener un formato válido.',
            'client_phone.required' => 'El teléfono es obligatorio.',
            'client_phone.regex' => 'El teléfono tiene un formato inválido.',
            'appointment_date.required' => 'La fecha de la cita es obligatoria.',
            'appointment_date.after_or_equal' => 'La fecha de la cita debe ser hoy o una fecha futura.',
            'appointment_date.before_or_equal' => 'No puedes agendar citas con más de 60 días de anticipación.',
            'appointment_time.required' => 'La hora de la cita es obligatoria.',
            'appointment_time.date_format' => 'El formato de la hora debe ser HH:MM.',
            'notes.max' => 'Las notas no pueden exceder 500 caracteres.',
            'terms_accepted.required' => 'Debes aceptar los términos y condiciones.',
            'terms_accepted.accepted' => 'Debes aceptar los términos y condiciones.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'client_name' => 'nombre',
            'client_document' => 'documento de identidad',
            'client_email' => 'correo electrónico',
            'client_phone' => 'teléfono',
            'appointment_date' => 'fecha de la cita',
            'appointment_time' => 'hora de la cita',
            'notes' => 'notas',
            'terms_accepted' => 'términos y condiciones',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validación personalizada: verificar que la fecha/hora no sea en el pasado
            $appointmentDateTime = $this->appointment_date . ' ' . $this->appointment_time;
            $dateTime = Carbon::parse($appointmentDateTime);
            
            if ($dateTime->isPast()) {
                $validator->errors()->add('appointment_time', 'No puedes agendar una cita en el pasado.');
            }
            
            // Validación: no permitir citas en fines de semana (opcional)
            if ($dateTime->isWeekend()) {
                $validator->errors()->add('appointment_date', 'No se pueden agendar citas en fines de semana.');
            }
            
            // Validación: horario de oficina (8:00 AM - 5:00 PM)
            $hour = $dateTime->hour;
            if ($hour < 8 || $hour >= 17) {
                $validator->errors()->add('appointment_time', 'Solo se pueden agendar citas entre 8:00 AM y 5:00 PM.');
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Limpiar y formatear los datos antes de la validación
        $this->merge([
            'client_name' => trim($this->client_name),
            'client_document' => trim($this->client_document),
            'client_email' => strtolower(trim($this->client_email)),
            'client_phone' => preg_replace('/[^0-9+\-\s()]/', '', $this->client_phone),
        ]);
    }
}