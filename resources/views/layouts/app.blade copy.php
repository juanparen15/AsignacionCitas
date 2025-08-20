{{-- resources/views/layouts/app.blade.php --}}
{{-- <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Sistema de Citas') - {{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Tailwind CSS CDN (para desarrollo rápido) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js (opcional para interactividad) -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <a href="/" class="text-xl font-bold text-gray-900">
                                {{ config('app.name', 'Sistema de Citas') }}
                            </a>
                        </div>
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            <a href="{{ route('appointments.index') }}" 
                               class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                                Reservar Cita
                            </a>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        @auth
                            <span class="text-sm text-gray-500">{{ auth()->user()->name }}</span>
                            <a href="{{ route('filament.admin.pages.dashboard') }}" 
                               class="text-sm text-blue-600 hover:text-blue-800">
                                Panel Admin
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main>
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded relative mx-4 mt-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative mx-4 mt-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative mx-4 mt-4" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-12">
            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                <div class="text-center text-gray-500">
                    <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.</p>
                </div>
            </div>
        </footer>
    </div>

    @stack('scripts')
</body>
</html> --}}




















<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistema de Agendamiento de Citas')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .step-active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .step-completed {
            background: #10b981;
        }
        
        .step-inactive {
            background: #e5e7eb;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="gradient-bg text-white shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Sistema de Agendamiento</h1>
                    <p class="text-blue-100">Agenda tu cita de manera fácil y rápida</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-blue-100">¿Necesitas consultar tu cita?</p>
                    <button 
                        onclick="consultarCita()" 
                        class="text-white hover:text-blue-200 font-medium underline"
                    >
                        Consultar aquí
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="container mx-auto px-4">
            <div class="text-center">
                <p class="text-gray-300">
                    © {{ date('Y') }} Sistema de Agendamiento de Citas. 
                    Todos los derechos reservados.
                </p>
                <p class="text-gray-400 text-sm mt-2">
                    Para soporte técnico contacte al administrador del sistema
                </p>
            </div>
        </div>
    </footer>

    <!-- Modal Consultar Cita -->
    <div id="consultarCitaModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg max-w-md w-full p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Consultar Cita</h3>
                    <button onclick="cerrarConsultarCita()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form id="consultarCitaForm">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Número de Cita
                            </label>
                            <input 
                                type="text" 
                                id="numero_cita_consulta"
                                placeholder="Ej: CT-20241201-0001"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required
                            >
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Número de Documento
                            </label>
                            <input 
                                type="number" 
                                id="numero_documento_consulta"
                                placeholder="Número de documento"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required
                            >
                        </div>
                        
                        <div class="flex space-x-3 pt-4">
                            <button 
                                type="submit"
                                class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors"
                            >
                                <i class="fas fa-search mr-2"></i>
                                Consultar
                            </button>
                            <button 
                                type="button"
                                onclick="cerrarConsultarCita()"
                                class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-400 transition-colors"
                            >
                                Cancelar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Configurar CSRF token para axios
        window.axios = axios;
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Funciones para modal de consulta
        function consultarCita() {
            document.getElementById('consultarCitaModal').classList.remove('hidden');
        }

        function cerrarConsultarCita() {
            document.getElementById('consultarCitaModal').classList.add('hidden');
            document.getElementById('consultarCitaForm').reset();
        }

        // Manejar envío del formulario de consulta
        document.getElementById('consultarCitaForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const numeroCita = document.getElementById('numero_cita_consulta').value;
            const numeroDocumento = document.getElementById('numero_documento_consulta').value;
            
            try {
                const response = await fetch('/consultar-cita', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        numero_cita: numeroCita,
                        numero_documento: numeroDocumento
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    const cita = result.cita;
                    const fechaFormateada = new Date(cita.fecha_cita).toLocaleDateString('es-CO');
                    const horaFormateada = cita.hora_cita.substring(0, 5);
                    
                    Swal.fire({
                        title: 'Cita Encontrada',
                        html: `
                            <div class="text-left space-y-2">
                                <p><strong>Número:</strong> ${cita.numero_cita}</p>
                                <p><strong>Ciudadano:</strong> ${cita.nombres} ${cita.apellidos}</p>
                                <p><strong>Trámite:</strong> ${cita.tramite.nombre}</p>
                                <p><strong>Fecha:</strong> ${fechaFormateada}</p>
                                <p><strong>Hora:</strong> ${horaFormateada}</p>
                                <p><strong>Estado:</strong> <span class="px-2 py-1 rounded text-sm bg-blue-100 text-blue-800">${cita.estado}</span></p>
                            </div>
                        `,
                        icon: 'success',
                        confirmButtonText: 'Cerrar'
                    });
                    cerrarConsultarCita();
                } else {
                    Swal.fire('Error', result.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error', 'Ocurrió un error al consultar la cita', 'error');
            }
        });

        // Cerrar modal al hacer clic fuera
        document.getElementById('consultarCitaModal').addEventListener('click', function(e) {
            if (e.target === this) {
                cerrarConsultarCita();
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>