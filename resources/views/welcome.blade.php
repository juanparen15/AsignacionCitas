{{-- resources/views/welcome.blade.php (página de citas Sisben) --}}
@extends('layouts.app')

@section('title', 'Citas Sisben')

@section('content')
    <div class="relative overflow-hidden bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
                <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                    <div class="sm:text-center lg:text-left">
                        <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                            <span class="block xl:inline">Agendamiento</span>
                            <span class="block text-green-600 xl:inline">Citas Sisben</span>
                        </h1>
                        <p
                            class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                            Sistema oficial para agendar tu cita de encuestamiento, actualización o certificación Sisben.
                            Selecciona el tipo de trámite, elige tu horario disponible y confirma tu cita.
                        </p>
                        <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                            <div class="rounded-md shadow">
                                <a href="{{ route('agendamiento.index') }}"
                                    class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 md:py-4 md:text-lg md:px-10 transition-colors">
                                    Agendar Cita Sisben
                                </a>
                            </div>
                            {{-- <div class="mt-3 sm:mt-0 sm:ml-3">
                                <a onclick="consultarCita()"
                                    class="w-full flex items-center justify-center px-8 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 md:py-4 md:text-lg md:px-10 transition-colors">
                                    Consultar mi Cita
                                </a>
                            </div> --}}
                            <button 
                            onclick="consultarCita()" 
                            class="bg-blue text-white-600 hover:bg-blue-50 font-semibold py-3 px-6 rounded-xl transition-all duration-200 transform hover:scale-105 shadow-lg"
                        >
                            <i class="fas fa-search mr-2"></i>
                            Consultar Cita
                        </button>
                        </div>
                    </div>
                </main>
            </div>
        </div>
        <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2">
            <div
                class="h-56 w-full bg-gradient-to-r from-green-500 to-blue-600 sm:h-72 md:h-96 lg:w-full lg:h-full flex items-center justify-center">
                <div class="text-white text-center">
                    <svg class="w-32 h-32 mx-auto mb-4" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 3c1.93 0 3.5 1.57 3.5 3.5S13.93 13 12 13s-3.5-1.57-3.5-3.5S10.07 6 12 6zm7 13H5v-.23c0-.62.28-1.2.76-1.58C7.47 15.82 9.64 15 12 15s4.53.82 6.24 2.19c.48.38.76.97.76 1.58V19z" />
                    </svg>
                    <h3 class="text-2xl font-bold">Sistema Sisben</h3>
                    <p class="mt-2">Agendamiento de citas oficial</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección de tipos de trámites Sisben -->
    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:text-center">
                <h2 class="text-base text-green-600 font-semibold tracking-wide uppercase">Trámites Sisben</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    ¿Qué trámite necesitas realizar?
                </p>
                <p class="mt-4 max-w-2xl text-xl text-gray-500 lg:mx-auto">
                    Agenda tu cita según el tipo de trámite que requieras. Todos los servicios son gratuitos.
                </p>
            </div>

            <div class="mt-10">
                <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">

                    <!-- Encuestamiento Primera Vez -->
                    <div class="relative bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-green-500 text-white mx-auto">
                            <svg fill="currentColor" viewBox="0 0 20 20" class="w-6 h-6">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg leading-6 font-medium text-gray-900 text-center">Primera Vez</h3>
                        <p class="mt-2 text-base text-gray-500 text-center">
                            Encuestamiento para personas que nunca han sido registradas en el Sisben.
                        </p>
                        <div class="mt-4 text-center">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Gratuito
                            </span>
                        </div>
                    </div>

                    <!-- Actualización de Datos -->
                    <div class="relative bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white mx-auto">
                            <svg fill="currentColor" viewBox="0 0 20 20" class="w-6 h-6">
                                <path
                                    d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6zm2 2h8v2H6V8zm0 4h4v2H6v-2z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg leading-6 font-medium text-gray-900 text-center">Actualización</h3>
                        <p class="mt-2 text-base text-gray-500 text-center">
                            Actualización de datos personales, familiares o cambio de dirección.
                        </p>
                        <div class="mt-4 text-center">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Gratuito
                            </span>
                        </div>
                    </div>

                    <!-- Certificación -->
                    <div class="relative bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-purple-500 text-white mx-auto">
                            <svg fill="currentColor" viewBox="0 0 20 20" class="w-6 h-6">
                                <path
                                    d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg leading-6 font-medium text-gray-900 text-center">Certificación</h3>
                        <p class="mt-2 text-base text-gray-500 text-center">
                            Expedición de certificados y constancias del puntaje Sisben.
                        </p>
                        <div class="mt-4 text-center">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                Gratuito
                            </span>
                        </div>
                    </div>

                    <!-- Cambio de Municipio -->
                    <div class="relative bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-orange-500 text-white mx-auto">
                            <svg fill="currentColor" viewBox="0 0 20 20" class="w-6 h-6">
                                <path
                                    d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                                <path
                                    d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1V8a1 1 0 00-1-1h-3z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg leading-6 font-medium text-gray-900 text-center">Cambio Municipio</h3>
                        <p class="mt-2 text-base text-gray-500 text-center">
                            Traslado de registro por cambio de municipio de residencia.
                        </p>
                        <div class="mt-4 text-center">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                Gratuito
                            </span>
                        </div>
                    </div>

                    <!-- Corrección de Datos -->
                    <div class="relative bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-red-500 text-white mx-auto">
                            <svg fill="currentColor" viewBox="0 0 20 20" class="w-6 h-6">
                                <path
                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg leading-6 font-medium text-gray-900 text-center">Corrección Datos</h3>
                        <p class="mt-2 text-base text-gray-500 text-center">
                            Corrección de errores en la información registrada en el sistema.
                        </p>
                        <div class="mt-4 text-center">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Gratuito
                            </span>
                        </div>
                    </div>

                    <!-- Consulta de Estado -->
                    <div class="relative bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white mx-auto">
                            <svg fill="currentColor" viewBox="0 0 20 20" class="w-6 h-6">
                                <path
                                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg leading-6 font-medium text-gray-900 text-center">Consulta Estado</h3>
                        <p class="mt-2 text-base text-gray-500 text-center">
                            Verificación del estado actual de tu registro en el Sisben.
                        </p>
                        <div class="mt-4 text-center">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                Gratuito
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-12 text-center">
                <a href="{{ route('agendamiento.index') }}"
                    class="inline-flex items-center px-8 py-4 border border-transparent text-lg font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition-colors shadow-lg">
                    Agendar mi Cita Sisben
                    <svg class="ml-2 w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Sección de información importante -->
    <div class="bg-blue-50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:text-center mb-8">
                <h2 class="text-2xl font-extrabold text-gray-900">Información Importante</h2>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="bg-white rounded-lg p-6 shadow">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Documentos Requeridos</h3>
                    <ul class="text-sm text-gray-600 space-y-2">
                        <li>• Cédula de ciudadanía original</li>
                        <li>• Comprobante de residencia</li>
                        <li>• Documento de identidad del jefe de hogar</li>
                    </ul>
                </div>

                <div class="bg-white rounded-lg p-6 shadow">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Horarios de Atención</h3>
                    <ul class="text-sm text-gray-600 space-y-2">
                        <li>• Lunes a Viernes: 8:00 AM - 5:00 PM</li>
                        <li>• Sábados, Domingos y festivos: Cerrado</li>
                        <li>• Tiempo promedio de atención: 30 minutos</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
