<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistema de Agendamiento de Citas')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Confetti.js for celebration effects -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .glass-effect {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }
        
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .animate-pulse-slow {
            animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        /* Loading animation */
        .loading-dots {
            display: inline-block;
        }
        
        .loading-dots::after {
            content: '';
            animation: dots 1.5s steps(5, end) infinite;
        }
        
        @keyframes dots {
            0%, 20% { content: ''; }
            40% { content: '.'; }
            60% { content: '..'; }
            80%, 100% { content: '...'; }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="relative overflow-hidden">
        <div class="gradient-bg text-white shadow-2xl">
            <!-- Decorative Elements -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-10 left-10 w-20 h-20 rounded-full bg-white animate-float"></div>
                <div class="absolute top-32 right-20 w-16 h-16 rounded-full bg-white animate-float" style="animation-delay: -2s;"></div>
                <div class="absolute bottom-10 left-1/3 w-12 h-12 rounded-full bg-white animate-float" style="animation-delay: -4s;"></div>
            </div>
            
            <div class="relative container mx-auto px-4 py-8 md:py-12">
                <div class="flex flex-col md:flex-row items-center justify-between">
                    <div class="text-center md:text-left mb-6 md:mb-0">
                        <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-2">
                            Sistema de Agendamiento
                        </h1>
                        <p class="text-lg md:text-xl text-blue-100 mb-4">
                            Agenda tu cita de manera fácil, rápida y segura
                        </p>
                        <div class="flex flex-wrap justify-center md:justify-start gap-4 text-sm">
                            <div class="flex items-center bg-white bg-opacity-20 rounded-full px-4 py-2">
                                <i class="fas fa-clock mr-2"></i>
                                <span>Disponible 24/7</span>
                            </div>
                            <div class="flex items-center bg-white bg-opacity-20 rounded-full px-4 py-2">
                                <i class="fas fa-shield-alt mr-2"></i>
                                <span>100% Seguro</span>
                            </div>
                            <div class="flex items-center bg-white bg-opacity-20 rounded-full px-4 py-2">
                                <i class="fas fa-mobile-alt mr-2"></i>
                                <span>Desde cualquier dispositivo</span>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl p-6 mb-4">
                            <i class="fas fa-calendar-check text-6xl mb-4 animate-pulse-slow"></i>
                            <p class="text-sm font-medium">¿Ya tienes una cita?</p>
                        </div>
                        <button 
                            onclick="consultarCita()" 
                            class="bg-white text-blue-600 hover:bg-blue-50 font-semibold py-3 px-6 rounded-xl transition-all duration-200 transform hover:scale-105 shadow-lg"
                        >
                            <i class="fas fa-search mr-2"></i>
                            Consultar Cita
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="relative">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12 mt-16">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-3 gap-8 mb-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">
                        <i class="fas fa-calendar-check mr-2"></i>
                        Sistema de Citas
                    </h3>
                    <p class="text-gray-400 mb-4">
                        Plataforma digital para el agendamiento eficiente de citas y trámites administrativos.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-facebook text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Enlaces Útiles</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition-colors">Términos y Condiciones</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Política de Privacidad</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Tratamiento de Datos</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Centro de Ayuda</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Contacto</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li class="flex items-center">
                            <i class="fas fa-phone mr-3"></i>
                            (57) 300 123 4567
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-3"></i>
                            contactenos@puertoboyaca-boyaca.gov.co
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-clock mr-3"></i>
                            Lun - Vie: 8:00 AM - 6:00 PM
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 pt-8 text-center">
                <p class="text-gray-400">
                    © {{ date('Y') }} Sistema de Agendamiento de Citas. 
                    Todos los derechos reservados.
                </p>
                <p class="text-gray-500 text-sm mt-2">
                    Desarrollado con tecnología segura y confiable
                </p>
            </div>
        </div>
    </footer>

    <!-- Modal Consultar Cita -->
    <div id="consultarCitaModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 backdrop-blur-sm">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-8 shadow-2xl transform transition-all duration-300 scale-95 modal-content">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-search text-2xl text-blue-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Consultar Cita</h3>
                    <p class="text-gray-600">Ingrese los datos para consultar su cita</p>
                </div>
                
                <form id="consultarCitaForm" class="space-y-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">
                            Número de Cita
                        </label>
                        <input 
                            type="text" 
                            id="numero_cita_consulta"
                            placeholder="Ej: CT-20241201-0001"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            required
                        >
                    </div>
                    
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">
                            Número de Documento
                        </label>
                        <input 
                            type="text" 
                            id="numero_documento_consulta"
                            placeholder="Número de documento"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            required
                        >
                    </div>
                    
                    <div class="flex space-x-3 pt-4">
                        <button 
                            type="submit"
                            class="flex-1 bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 px-6 rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-200 font-semibold shadow-lg"
                        >
                            <i class="fas fa-search mr-2"></i>
                            Consultar
                        </button>
                        <button 
                            type="button"
                            onclick="cerrarConsultarCita()"
                            class="flex-1 bg-gray-200 text-gray-700 py-3 px-6 rounded-xl hover:bg-gray-300 transition-colors duration-200 font-medium"
                        >
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-white bg-opacity-90 backdrop-blur-sm hidden z-50 flex items-center justify-center">
        <div class="text-center">
            <div class="w-16 h-16 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin mx-auto mb-4"></div>
            <p class="text-gray-600 font-medium">Cargando<span class="loading-dots"></span></p>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Configurar CSRF token para axios
        if (typeof axios !== 'undefined') {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        }

        // Smooth scroll polyfill for older browsers
        if (!('scrollBehavior' in document.documentElement.style)) {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/gh/iamdustan/smoothscroll@master/src/smoothscroll.js';
            document.head.appendChild(script);
        }

        // Modal animations
        function consultarCita() {
            const modal = document.getElementById('consultarCitaModal');
            const content = modal.querySelector('.modal-content');
            
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95');
                content.classList.add('scale-100');
            }, 10);
        }

        function cerrarConsultarCita() {
            const modal = document.getElementById('consultarCitaModal');
            const content = modal.querySelector('.modal-content');
            
            content.classList.remove('scale-100');
            content.classList.add('scale-95');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                document.getElementById('consultarCitaForm').reset();
            }, 200);
        }

        // Loading overlay functions
        function showLoading() {
            document.getElementById('loadingOverlay').classList.remove('hidden');
        }

        function hideLoading() {
            document.getElementById('loadingOverlay').classList.add('hidden');
        }

        // Enhanced form submission with loading
        document.getElementById('consultarCitaForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const numeroCita = document.getElementById('numero_cita_consulta').value;
            const numeroDocumento = document.getElementById('numero_documento_consulta').value;
            
            // Show loading
            showLoading();
            
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
                    const fechaFormateada = new Date(cita.fecha_cita).toLocaleDateString('es-CO', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                    const horaFormateada = cita.hora_cita.substring(0, 5);
                    
                    // Close modal first
                    cerrarConsultarCita();
                    
                    // Show beautiful result
                    Swal.fire({
                        title: '<i class="fas fa-check-circle text-green-500"></i> Cita Encontrada',
                        html: `
                            <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-lg p-6 text-left space-y-3 mt-4">
                                <div class="flex justify-between items-center py-2 border-b border-green-200">
                                    <span class="font-semibold text-gray-700">Número:</span>
                                    <span class="font-bold text-green-700 bg-white px-3 py-1 rounded-full">${cita.numero_cita}</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-green-200">
                                    <span class="font-semibold text-gray-700">Ciudadano:</span>
                                    <span class="text-gray-800">${cita.nombres} ${cita.apellidos}</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-green-200">
                                    <span class="font-semibold text-gray-700">Trámite:</span>
                                    <span class="text-gray-800 text-right">${cita.tramite.nombre}</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-green-200">
                                    <span class="font-semibold text-gray-700">Fecha:</span>
                                    <span class="text-gray-800">${fechaFormateada}</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-green-200">
                                    <span class="font-semibold text-gray-700">Hora:</span>
                                    <span class="text-gray-800 font-medium">${horaFormateada}</span>
                                </div>
                                <div class="flex justify-between items-center py-2">
                                    <span class="font-semibold text-gray-700">Estado:</span>
                                    <span class="px-3 py-1 rounded-full text-sm font-medium ${getStatusClass(cita.estado)}">${getStatusText(cita.estado)}</span>
                                </div>
                            </div>
                        `,
                        confirmButtonText: '<i class="fas fa-times mr-2"></i>Cerrar',
                        confirmButtonColor: '#3b82f6',
                        width: '600px',
                        showClass: {
                            popup: 'animate__animated animate__fadeInDown'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOutUp'
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Cita No Encontrada',
                        text: result.message,
                        icon: 'error',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#ef4444'
                    });
                }
            } catch (error) {
                Swal.fire({
                    title: 'Error de Conexión',
                    text: 'No se pudo conectar con el servidor. Por favor, intente nuevamente.',
                    icon: 'error',
                    confirmButtonText: 'Reintentar',
                    confirmButtonColor: '#ef4444'
                });
            } finally {
                hideLoading();
            }
        });

        // Helper functions for status styling
        function getStatusClass(status) {
            const classes = {
                'programada': 'bg-yellow-100 text-yellow-800',
                'confirmada': 'bg-blue-100 text-blue-800',
                'en_proceso': 'bg-purple-100 text-purple-800',
                'atendida': 'bg-green-100 text-green-800',
                'cancelada': 'bg-red-100 text-red-800',
                'no_asistio': 'bg-gray-100 text-gray-800'
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        }

        function getStatusText(status) {
            const texts = {
                'programada': 'Programada',
                'confirmada': 'Confirmada',
                'en_proceso': 'En Proceso',
                'atendida': 'Atendida',
                'cancelada': 'Cancelada',
                'no_asistio': 'No Asistió'
            };
            return texts[status] || status;
        }

        // Close modal when clicking outside
        document.getElementById('consultarCitaModal').addEventListener('click', function(e) {
            if (e.target === this) {
                cerrarConsultarCita();
            }
        });

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('consultarCitaModal');
                if (!modal.classList.contains('hidden')) {
                    cerrarConsultarCita();
                }
            }
        });

        // Intersection Observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate__animated', 'animate__fadeInUp');
                }
            });
        }, observerOptions);

        // Observe elements when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Add animation classes to elements
            const animatedElements = document.querySelectorAll('[data-animate]');
            animatedElements.forEach(el => observer.observe(el));
            
            // Add loading states to buttons
            const buttons = document.querySelectorAll('button[type="submit"]');
            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    if (this.form && this.form.checkValidity()) {
                        this.classList.add('loading');
                    }
                });
            });
        });

        // Progressive Web App features
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                // Register service worker for offline functionality
                // navigator.serviceWorker.register('/sw.js');
            });
        }

        // Analytics and performance monitoring
        window.addEventListener('load', function() {
            // Track page load time
            const loadTime = performance.now();
            console.log(`Page loaded in ${Math.round(loadTime)}ms`);
        });

        // Custom error handling
        window.addEventListener('error', function(e) {
            console.error('JavaScript Error:', e.error);
            // You can send this to your error tracking service
        });

        // Responsive utilities
        function isMobile() {
            return window.innerWidth <= 768;
        }

        function isTablet() {
            return window.innerWidth > 768 && window.innerWidth <= 1024;
        }

        function isDesktop() {
            return window.innerWidth > 1024;
        }

        // Update viewport height for mobile browsers
        function updateViewportHeight() {
            const vh = window.innerHeight * 0.01;
            document.documentElement.style.setProperty('--vh', `${vh}px`);
        }

        window.addEventListener('resize', updateViewportHeight);
        updateViewportHeight();
    </script>
    
    <!-- Animate.css for enhanced animations -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css">
    
    @stack('scripts')
</body>
</html>