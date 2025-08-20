{{-- // resources/views/appointments/cancelled.blade.php --}}
@extends('layouts.app')

@section('title', 'Cita Cancelada')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto text-center">
        <div class="bg-red-50 border border-red-200 rounded-lg p-8">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            
            <h1 class="text-2xl font-bold text-red-900 mb-4">Cita Cancelada</h1>
            
            <div class="bg-white rounded-lg p-6 mb-6">
                <h2 class="text-lg font-semibold mb-4">Detalles de la Cita Cancelada</h2>
                
                <div class="space-y-3 text-left">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Servicio:</span>
                        <span class="font-medium">{{ $appointment->service->name }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">Fecha y Hora:</span>
                        <span class="font-medium">{{ $appointment->appointment_date->format('d/m/Y H:i') }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">Cliente:</span>
                        <span class="font-medium">{{ $appointment->client_name }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">Estado:</span>
                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-sm">Cancelada</span>
                    </div>
                </div>
            </div>
            
            <div class="text-sm text-gray-600 mb-6">
                <p>Tu cita ha sido cancelada exitosamente.</p>
                <p>Si deseas reagendar, puedes hacer una nueva reserva en cualquier momento.</p>
            </div>
            
            <div class="space-y-3">
                <a href="{{ route('appointments.index') }}" 
                   class="inline-block bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors">
                    Reservar Nueva Cita
                </a>
                
                <br>
                
                <a href="/" class="text-blue-600 hover:text-blue-800">
                    Volver al Inicio
                </a>
            </div>
        </div>
    </div>
</div>
@endsection