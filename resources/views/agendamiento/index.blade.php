<!-- resources/views/agendamiento/index.blade.php -->
@extends('layouts.app')

@section('title', 'Agendar Cita')

@section('content')
    <div x-data="agendamientoApp()" class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 py-8 px-4">
        <div class="max-w-5xl mx-auto">
            <!-- Header Section -->
            <div class="text-center mb-8">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-3">
                    <i class="fas fa-calendar-check text-blue-600 mr-3"></i>
                    Agendar Cita
                </h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Complete el proceso paso a paso para agendar su cita de manera fácil y rápida
                </p>
            </div>

            <!-- Progress Steps - Responsive -->
            <div class="mb-8 bg-white rounded-xl shadow-sm p-4 md:p-6">
                <!-- Desktop Progress -->
                <div class="hidden md:flex items-center justify-between">
                    <div class="flex items-center flex-1">
                        <!-- Step 1 -->
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-medium transition-all duration-300 shadow-md"
                                :class="step >= 1 ? (step > 1 ? 'bg-green-500' : 'bg-blue-600 scale-110') : 'bg-gray-300'">
                                <i class="fas fa-building" x-show="step === 1"></i>
                                <i class="fas fa-check" x-show="step > 1"></i>
                                <span x-show="step < 1">1</span>
                            </div>
                            <span class="ml-3 text-sm font-medium transition-colors"
                                :class="step >= 1 ? 'text-gray-900' : 'text-gray-500'">
                                Seleccionar Trámite
                            </span>
                        </div>

                        <!-- Connector -->
                        <div class="flex-1 h-2 bg-gray-200 mx-6 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-blue-500 to-green-500 transition-all duration-500 ease-out"
                                :style="{ width: step >= 2 ? '100%' : '0%' }"></div>
                        </div>

                        <!-- Step 2 -->
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-medium transition-all duration-300 shadow-md"
                                :class="step >= 2 ? (step > 2 ? 'bg-green-500' : 'bg-blue-600 scale-110') : 'bg-gray-300'">
                                <i class="fas fa-calendar" x-show="step === 2"></i>
                                <i class="fas fa-check" x-show="step > 2"></i>
                                <span x-show="step < 2">2</span>
                            </div>
                            <span class="ml-3 text-sm font-medium transition-colors"
                                :class="step >= 2 ? 'text-gray-900' : 'text-gray-500'">
                                Fecha y Hora
                            </span>
                        </div>

                        <!-- Connector -->
                        <div class="flex-1 h-2 bg-gray-200 mx-6 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-blue-500 to-green-500 transition-all duration-500 ease-out"
                                :style="{ width: step >= 3 ? '100%' : '0%' }"></div>
                        </div>

                        <!-- Step 3 -->
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-medium transition-all duration-300 shadow-md"
                                :class="step >= 3 ? (step > 3 ? 'bg-green-500' : 'bg-blue-600 scale-110') : 'bg-gray-300'">
                                <i class="fas fa-user" x-show="step === 3"></i>
                                <i class="fas fa-check" x-show="step > 3"></i>
                                <span x-show="step < 3">3</span>
                            </div>
                            <span class="ml-3 text-sm font-medium transition-colors"
                                :class="step >= 3 ? 'text-gray-900' : 'text-gray-500'">
                                Datos Personales
                            </span>
                        </div>

                        <!-- Connector -->
                        <div class="flex-1 h-2 bg-gray-200 mx-6 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-blue-500 to-green-500 transition-all duration-500 ease-out"
                                :style="{ width: step >= 4 ? '100%' : '0%' }"></div>
                        </div>

                        <!-- Step 4 -->
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-medium transition-all duration-300 shadow-md"
                                :class="step >= 4 ? 'bg-green-500 scale-110' : 'bg-gray-300'">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <span class="ml-3 text-sm font-medium transition-colors"
                                :class="step >= 4 ? 'text-gray-900' : 'text-gray-500'">
                                Confirmación
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Mobile Progress -->
                <div class="md:hidden">
                    <div class="flex items-center justify-center mb-4">
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-medium text-gray-600">Paso</span>
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-bold bg-blue-600"
                                x-text="step"></div>
                            <span class="text-sm font-medium text-gray-600">de 4</span>
                        </div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-gradient-to-r from-blue-500 to-green-500 h-3 rounded-full transition-all duration-500 ease-out"
                            :style="{ width: (step / 4) * 100 + '%' }"></div>
                    </div>
                    <div class="text-center mt-3">
                        <span class="text-sm font-medium text-gray-700">
                            <span x-show="step === 1">Seleccionar Trámite</span>
                            <span x-show="step === 2">Fecha y Hora</span>
                            <span x-show="step === 3">Datos Personales</span>
                            <span x-show="step === 4">Confirmación</span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Step Content Container -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <!-- Step 1: Seleccionar Trámite -->
                <div x-show="step === 1" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform translate-x-4"
                    x-transition:enter-end="opacity-100 transform translate-x-0" class="p-6 md:p-8">
                    <div class="text-center mb-8">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-building text-2xl text-blue-600"></i>
                        </div>
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">Seleccione el trámite</h2>
                        <p class="text-gray-600">Escoja la secretaría, área y trámite que desea realizar</p>
                    </div>

                    <!-- Secretarías -->
                    <div class="space-y-4 max-w-4xl mx-auto">
                        <template x-for="secretaria in secretarias" :key="secretaria.id">
                            <div
                                class="border-2 border-gray-100 rounded-xl overflow-hidden hover:border-blue-200 transition-all duration-200 hover:shadow-md">
                                <button @click="toggleSecretaria(secretaria.id)"
                                    class="w-full px-6 py-5 bg-gradient-to-r from-gray-50 to-gray-100 hover:from-blue-50 hover:to-purple-50 flex items-center justify-between transition-all duration-200">
                                    <div class="flex items-center">
                                        <div
                                            class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center mr-4">
                                            <i class="fas fa-building text-white text-lg"></i>
                                        </div>
                                        <span class="font-semibold text-gray-800 text-left"
                                            x-text="secretaria.nombre"></span>
                                    </div>
                                    <i class="fas fa-chevron-down transition-transform duration-200 text-blue-600"
                                        :class="secretariaAbierta === secretaria.id ? 'rotate-180' : ''"></i>
                                </button>

                                <div x-show="secretariaAbierta === secretaria.id"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 max-h-0"
                                    x-transition:enter-end="opacity-100 max-h-screen" class="border-t-2 border-gray-200">
                                    <!-- Áreas -->
                                    <template x-for="area in secretaria.areas_activas" :key="area.id">
                                        <div class="border-b border-gray-100 last:border-b-0">
                                            <button @click="toggleArea(area.id)"
                                                class="w-full px-8 py-4 bg-white hover:bg-gray-50 flex items-center justify-between transition-colors duration-150">
                                                <div class="flex items-center">
                                                    <div
                                                        class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                                        <i class="fas fa-sitemap text-green-600"></i>
                                                    </div>
                                                    <span class="text-gray-700 font-medium" x-text="area.nombre"></span>
                                                </div>
                                                <i class="fas fa-chevron-down transition-transform duration-150 text-green-600"
                                                    :class="areaAbierta === area.id ? 'rotate-180' : ''"></i>
                                            </button>

                                            <div x-show="areaAbierta === area.id"
                                                x-transition:enter="transition ease-out duration-150"
                                                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                                class="bg-gray-50">
                                                <!-- Trámites -->
                                                <template x-for="tramite in area.tramites_activos" :key="tramite.id">
                                                    <button @click="seleccionarTramite(tramite)"
                                                        class="w-full px-12 py-5 text-left hover:bg-blue-50 border-b border-gray-200 last:border-b-0 transition-all duration-150 group hover:pl-14">
                                                        <div class="flex items-center justify-between">
                                                            <div class="flex-1">
                                                                <div class="flex items-center">
                                                                    <div
                                                                        class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3 group-hover:bg-purple-200 transition-colors">
                                                                        <i
                                                                            class="fas fa-file-alt text-purple-600 text-sm"></i>
                                                                    </div>
                                                                    <h4 class="font-semibold text-gray-800 group-hover:text-blue-700 transition-colors"
                                                                        x-text="tramite.nombre"></h4>
                                                                </div>
                                                                <p class="text-sm text-gray-600 mt-2 ml-11"
                                                                    x-text="tramite.descripcion"></p>
                                                                <div
                                                                    class="flex items-center mt-3 ml-11 space-x-4 flex-wrap gap-2">
                                                                    <span
                                                                        class="text-sm px-3 py-1 rounded-full font-medium"
                                                                        :class="tramite.es_gratuito ?
                                                                            'bg-green-100 text-green-700' :
                                                                            'bg-yellow-100 text-yellow-700'"
                                                                        x-text="tramite.es_gratuito ? 'Gratuito' : '$' + new Intl.NumberFormat('es-CO').format(tramite.costo)"></span>
                                                                    <span
                                                                        class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                                                                        <i class="fas fa-clock mr-1"></i>
                                                                        <span x-text="tramite.duracion_minutos"></span> min
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <i
                                                                class="fas fa-arrow-right text-blue-600 opacity-0 group-hover:opacity-100 transition-all duration-200 ml-4"></i>
                                                        </div>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Step 2: Seleccionar Fecha y Hora -->
                <div x-show="step === 2" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform translate-x-4"
                    x-transition:enter-end="opacity-100 transform translate-x-0" class="p-6 md:p-8">
                    <div class="text-center mb-8">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-calendar text-2xl text-blue-600"></i>
                        </div>
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">Fecha y Hora</h2>
                        <p class="text-gray-600">Seleccione la fecha y hora que mejor le convenga</p>
                    </div>

                    <div class="max-w-4xl mx-auto">
                        <!-- Información del trámite seleccionado -->
                        <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl p-6 mb-8 border border-blue-100">
                            <h3 class="font-semibold text-blue-800 mb-3">Trámite Seleccionado:</h3>
                            <div class="space-y-2">
                                <p class="text-blue-700 font-medium text-lg" x-text="tramiteSeleccionado?.nombre"></p>
                                <div class="flex items-center space-x-4 mt-3">
                                    <span class="text-sm px-3 py-1 rounded-full font-medium"
                                        :class="tramiteSeleccionado?.es_gratuito ? 'bg-green-100 text-green-700' :
                                            'bg-yellow-100 text-yellow-700'"
                                        x-text="tramiteSeleccionado?.es_gratuito ? 'Gratuito' : '$' + new Intl.NumberFormat('es-CO').format(tramiteSeleccionado?.costo)"></span>
                                    <span class="text-sm bg-gray-100 text-gray-600 px-3 py-1 rounded-full">
                                        <i class="fas fa-clock mr-1"></i>
                                        <span x-text="tramiteSeleccionado?.duracion_minutos"></span> min
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Información de Horarios de Atención -->
                        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 mb-6">
                            <div class="flex items-start">
                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-yellow-100 flex-shrink-0">
                                    <i class="fas fa-clock text-yellow-600"></i>
                                </div>
                                <div class="ml-4">
                                    <h4 class="font-semibold text-yellow-800 mb-3">Horarios de Atención</h4>
                                    <div class="grid md:grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <p class="text-yellow-700 mb-2">
                                                <i class="fas fa-sun mr-2"></i>
                                                <strong>Horario de mañana:</strong> 8:00 AM - 12:00 PM
                                            </p>
                                            <p class="text-yellow-700">
                                                <i class="fas fa-moon mr-2"></i>
                                                <strong>Horario de tarde:</strong> 2:00 PM - 5:00 PM
                                            </p>
                                        </div>
                                        <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                                            <p class="text-red-700 font-medium mb-1">
                                                <i class="fas fa-utensils mr-2"></i>
                                                Horario de Almuerzo
                                            </p>
                                            <p class="text-red-600 text-sm">
                                                12:00 PM - 2:00 PM<br>
                                                <span class="text-xs">No disponible para citas</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid lg:grid-cols-2 gap-8">
                            <!-- Selección de Fecha -->
                            <div class="space-y-4">
                                <label class="block text-lg font-semibold text-gray-700">Seleccione la Fecha</label>
                                <input type="date" x-model="fechaSeleccionada" @change="cargarHorasDisponibles()"
                                    :min="fechaMinima" :max="fechaMaxima"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg">
                                <p class="text-sm text-gray-500 bg-gray-50 p-3 rounded-lg">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Disponible desde <strong x-text="fechaMinima"></strong> hasta <strong
                                        x-text="fechaMaxima"></strong>
                                </p>
                            </div>

                            <!-- Selección de Hora -->
                            <div class="space-y-4">
                                <label class="block text-lg font-semibold text-gray-700">Seleccione la Hora</label>
                                <div class="space-y-3 max-h-80 overflow-y-auto bg-gray-50 rounded-xl p-4">
                                    <template x-for="hora in horasDisponibles" :key="hora.hora">
                                        <button @click="horaSeleccionada = hora.hora"
                                            class="w-full px-4 py-4 border-2 rounded-xl text-left transition-all duration-200 hover:shadow-md"
                                            :class="horaSeleccionada === hora.hora ?
                                                'border-blue-500 bg-blue-50 text-blue-700 shadow-md' :
                                                'border-gray-200 hover:border-blue-300 bg-white'">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center">
                                                    <div class="w-3 h-3 rounded-full mr-3"
                                                        :class="horaSeleccionada === hora.hora ? 'bg-blue-500' : 'bg-gray-300'">
                                                    </div>
                                                    <span class="font-semibold text-lg" x-text="hora.hora"></span>
                                                </div>
                                                <div class="text-right">
                                                    <span class="text-sm font-medium"
                                                        :class="horaSeleccionada === hora.hora ? 'text-blue-600' :
                                                            'text-gray-500'">
                                                        <span x-text="hora.disponibles"></span>/<span
                                                            x-text="hora.total"></span> disponibles
                                                    </span>
                                                    <div class="w-16 h-2 bg-gray-200 rounded-full mt-1">
                                                        <div class="h-full bg-green-400 rounded-full transition-all duration-300"
                                                            :style="{ width: (hora.disponibles / hora.total) * 100 + '%' }">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </button>
                                    </template>
                                    <div x-show="horasDisponibles.length === 0 && fechaSeleccionada"
                                        class="text-center py-12 text-gray-500">
                                        <i class="fas fa-calendar-times text-6xl mb-4 text-gray-300"></i>
                                        <p class="text-lg font-medium">No hay horarios disponibles</p>
                                        <p class="text-sm">para esta fecha</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="flex flex-col sm:flex-row justify-between gap-4 mt-8 max-w-4xl mx-auto">
                        <button @click="step = 1"
                            class="px-8 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors duration-200 font-medium">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Anterior
                        </button>
                        <button @click="avanzarPaso()" :disabled="!fechaSeleccionada || !horaSeleccionada"
                            class="px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:from-blue-700 hover:to-purple-700 disabled:from-gray-400 disabled:to-gray-400 disabled:cursor-not-allowed transition-all duration-200 font-medium shadow-lg disabled:shadow-none">
                            Siguiente
                            <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 3: Datos Personales -->
                <div x-show="step === 3" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform translate-x-4"
                    x-transition:enter-end="opacity-100 transform translate-x-0" class="p-6 md:p-8">
                    <div class="text-center mb-8">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-user text-2xl text-blue-600"></i>
                        </div>
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">Datos Personales</h2>
                        <p class="text-gray-600">Complete la información requerida para finalizar su cita</p>
                    </div>

                    <form @submit.prevent="agendarCita()" class="max-w-4xl mx-auto">
                        <div class="grid md:grid-cols-2 gap-6 mb-8">
                            <!-- Tipo de Documento -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">
                                    Tipo de Documento <span class="text-red-500">*</span>
                                </label>
                                <select x-model="datosPersonales.tipo_documento"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    required>
                                    <option value="">Seleccione...</option>
                                    <option value="CC">Cédula de Ciudadanía</option>
                                    <option value="CE">Cédula de Extranjería</option>
                                    <option value="PA">Pasaporte</option>
                                    <option value="TI">Tarjeta de Identidad</option>
                                    <option value="RC">Registro Civil</option>
                                </select>
                            </div>

                            <!-- Número de Documento -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">
                                    Número de Documento <span class="text-red-500">*</span>
                                </label>
                                <input type="number" x-model="datosPersonales.numero_documento" maxlength="20"
                                    placeholder="Solo números"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    required>
                            </div>

                            <!-- Nombres -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">
                                    Nombres <span class="text-red-500">*</span>
                                </label>
                                <input type="text" x-model="datosPersonales.nombres" pattern="[a-zA-ZÀ-ÿ\s]+"
                                    maxlength="100" placeholder="Solo letras y espacios"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    required>
                            </div>

                            <!-- Apellidos -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">
                                    Apellidos <span class="text-red-500">*</span>
                                </label>
                                <input type="text" x-model="datosPersonales.apellidos" pattern="[a-zA-ZÀ-ÿ\s]+"
                                    maxlength="100" placeholder="Solo letras y espacios"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    required>
                            </div>

                            <!-- Email -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">
                                    Correo Electrónico <span class="text-red-500">*</span>
                                </label>
                                <input type="email" x-model="datosPersonales.email" maxlength="150"
                                    placeholder="ejemplo@correo.com"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    required>
                            </div>

                            <!-- Teléfono -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">
                                    Teléfono <span class="text-red-500">*</span>
                                </label>
                                <input type="number" x-model="datosPersonales.telefono" maxlength="15"
                                    placeholder="3001234567"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    required>
                            </div>

                            <!-- Dirección (Opcional) -->
                            <div class="md:col-span-2 space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">
                                    Dirección (Opcional)
                                </label>
                                <input type="text" x-model="datosPersonales.direccion" maxlength="200"
                                    placeholder="Calle 123 # 45-67"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            </div>
                        </div>

                        <!-- Tratamiento de Datos -->
                        <div
                            class="bg-gradient-to-r from-blue-50 to-purple-50 border-2 border-blue-100 rounded-xl p-6 mb-8">
                            <div class="flex items-start">
                                <input type="checkbox" x-model="datosPersonales.acepta_tratamiento_datos"
                                    id="tratamiento_datos"
                                    class="mt-1 mr-4 w-5 h-5 text-blue-600 border-2 border-gray-300 rounded focus:ring-blue-500"
                                    required>
                                <label for="tratamiento_datos" class="text-sm text-gray-700 flex-1">
                                    <span class="font-semibold text-blue-800">Acepto el tratamiento de mis datos
                                        personales</span> <span class="text-red-500">*</span>
                                    <br>
                                    <span class="text-xs text-gray-600 leading-relaxed">
                                        Al marcar esta casilla, autorizo el tratamiento de mis datos personales conforme a
                                        la
                                        <a href="#" class="text-blue-600 hover:underline font-medium">Política de
                                            Privacidad</a>
                                        para los fines relacionados con el agendamiento y gestión de citas.
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="flex flex-col sm:flex-row justify-between gap-4">
                            <button type="button" @click="step = 2"
                                class="px-8 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors duration-200 font-medium">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Anterior
                            </button>
                            <button type="submit" :disabled="!formularioValido() || enviando"
                                class="px-8 py-4 bg-gradient-to-r from-green-600 to-blue-600 text-white rounded-xl hover:from-green-700 hover:to-blue-700 disabled:from-gray-400 disabled:to-gray-400 disabled:cursor-not-allowed transition-all duration-200 font-semibold shadow-lg disabled:shadow-none flex items-center justify-center">
                                <span x-show="!enviando" class="flex items-center">
                                    <i class="fas fa-check mr-2"></i>
                                    Agendar Cita
                                </span>
                                <span x-show="enviando" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Procesando...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Step 4: Confirmación -->
                <div x-show="step === 4" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform translate-x-4"
                    x-transition:enter-end="opacity-100 transform translate-x-0" class="p-6 md:p-8">
                    <div class="text-center max-w-3xl mx-auto">
                        <div class="mb-8">
                            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-check-circle text-4xl text-green-600"></i>
                            </div>
                            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">¡Cita Agendada Exitosamente!</h2>
                            <p class="text-lg text-gray-600">Su cita ha sido confirmada. Guarde la información para futuras
                                consultas.</p>
                        </div>

                        <!-- Información de la Cita -->
                        <div
                            class="bg-gradient-to-r from-green-50 to-blue-50 border-2 border-green-200 rounded-2xl p-8 mb-8 shadow-lg">
                            <div class="grid gap-4 text-left">
                                <div
                                    class="flex flex-col sm:flex-row sm:justify-between items-start sm:items-center py-3 border-b border-green-200 last:border-b-0">
                                    <span class="font-semibold text-gray-700 mb-2 sm:mb-0">Número de Cita:</span>
                                    <span class="font-bold text-xl text-green-700 bg-white px-4 py-2 rounded-lg shadow-sm"
                                        x-text="citaCreada?.numero_cita"></span>
                                </div>
                                <div
                                    class="flex flex-col sm:flex-row sm:justify-between items-start sm:items-center py-3 border-b border-green-200 last:border-b-0">
                                    <span class="font-semibold text-gray-700 mb-2 sm:mb-0">Ciudadano:</span>
                                    <span class="text-gray-800 font-medium"
                                        x-text="citaCreada?.nombres + ' ' + citaCreada?.apellidos"></span>
                                </div>
                                <div
                                    class="flex flex-col sm:flex-row sm:justify-between items-start sm:items-center py-3 border-b border-green-200 last:border-b-0">
                                    <span class="font-semibold text-gray-700 mb-2 sm:mb-0">Trámite:</span>
                                    <span class="text-gray-800 font-medium text-right"
                                        x-text="citaCreada?.tramite?.nombre"></span>
                                </div>
                                <div
                                    class="flex flex-col sm:flex-row sm:justify-between items-start sm:items-center py-3 border-b border-green-200 last:border-b-0">
                                    <span class="font-semibold text-gray-700 mb-2 sm:mb-0">Fecha:</span>
                                    <span class="text-gray-800 font-medium flex items-center">
                                        <i class="fas fa-calendar mr-2 text-blue-600"></i>
                                        <span x-text="formatearFecha(citaCreada?.fecha_cita)"></span>
                                    </span>
                                </div>
                                <div
                                    class="flex flex-col sm:flex-row sm:justify-between items-start sm:items-center py-3 border-b border-green-200 last:border-b-0">
                                    <span class="font-semibold text-gray-700 mb-2 sm:mb-0">Hora:</span>
                                    <span class="text-gray-800 font-medium flex items-center">
                                        <i class="fas fa-clock mr-2 text-blue-600"></i>
                                        <span x-text="citaCreada?.hora_cita?.substring(0, 5)"></span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-4">
                            <button @click="descargarPDF()"
                                class="w-full bg-gradient-to-r from-red-600 to-pink-600 text-white py-4 px-8 rounded-xl hover:from-red-700 hover:to-pink-700 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                <i class="fas fa-file-pdf mr-3 text-lg"></i>
                                Descargar Comprobante PDF
                            </button>

                            <button @click="reiniciar()"
                                class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-4 px-8 rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                <i class="fas fa-plus mr-3 text-lg"></i>
                                Agendar Nueva Cita
                            </button>
                        </div>

                        <!-- Important Information -->
                        <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-triangle text-yellow-600 text-xl mr-4 mt-1"></i>
                                <div class="text-left">
                                    <h4 class="font-semibold text-yellow-800 mb-3">Información Importante:</h4>
                                    <ul class="text-sm text-yellow-700 space-y-2">
                                        <li>• <strong>Guarde</strong> este número de cita para futuras consultas</li>
                                        <li>• <strong>Llegue 15 minutos antes</strong> de su cita con los documentos
                                            requeridos</li>
                                        <li>• Si no puede asistir, <strong>comuníquese con anticipación</strong> para
                                            reprogramar</li>
                                        <li>• La cita se cancelará automáticamente si llega <strong>más de 15 minutos
                                                tarde</strong></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
        <script>
            function agendamientoApp() {
                return {
                    step: 1,
                    secretarias: @json($secretarias),
                    secretariaAbierta: null,
                    areaAbierta: null,
                    tramiteSeleccionado: null,
                    fechaSeleccionada: '',
                    horaSeleccionada: '',
                    fechaMinima: '',
                    fechaMaxima: '',
                    horasDisponibles: [],
                    enviando: false,
                    citaCreada: null,
                    datosPersonales: {
                        tipo_documento: '',
                        numero_documento: '',
                        nombres: '',
                        apellidos: '',
                        email: '',
                        telefono: '',
                        direccion: '',
                        acepta_tratamiento_datos: false
                    },

                    toggleSecretaria(secretariaId) {
                        this.secretariaAbierta = this.secretariaAbierta === secretariaId ? null : secretariaId;
                        this.areaAbierta = null;
                    },

                    toggleArea(areaId) {
                        this.areaAbierta = this.areaAbierta === areaId ? null : areaId;
                    },

                    async seleccionarTramite(tramite) {
                        this.tramiteSeleccionado = tramite;

                        try {
                            const response = await axios.get(`/tramites/${tramite.id}/configuracion`);
                            const data = response.data;

                            this.fechaMinima = data.fecha_minima;
                            this.fechaMaxima = data.fecha_maxima;
                            this.step = 2;

                            // Smooth scroll to top
                            window.scrollTo({
                                top: 0,
                                behavior: 'smooth'
                            });
                        } catch (error) {
                            Swal.fire('Error', 'No se pudo cargar la configuración del trámite', 'error');
                        }
                    },

                    async cargarHorasDisponibles() {
                        if (!this.fechaSeleccionada || !this.tramiteSeleccionado) return;

                        try {
                            const response = await axios.get(
                                `/tramites/${this.tramiteSeleccionado.id}/horas/${this.fechaSeleccionada}`);
                            this.horasDisponibles = response.data;
                            this.horaSeleccionada = '';
                        } catch (error) {
                            this.horasDisponibles = [];
                            Swal.fire('Error', 'No se pudieron cargar los horarios disponibles', 'error');
                        }
                    },

                    avanzarPaso() {
                        if (this.step === 2 && this.fechaSeleccionada && this.horaSeleccionada) {
                            this.step = 3;
                            window.scrollTo({
                                top: 0,
                                behavior: 'smooth'
                            });
                        }
                    },

                    formularioValido() {
                        return this.datosPersonales.tipo_documento &&
                            this.datosPersonales.numero_documento &&
                            this.datosPersonales.nombres &&
                            this.datosPersonales.apellidos &&
                            this.datosPersonales.email &&
                            this.datosPersonales.telefono &&
                            this.datosPersonales.acepta_tratamiento_datos;
                    },

                    async agendarCita() {
                        if (!this.formularioValido()) return;

                        this.enviando = true;

                        try {
                            const response = await axios.post('/agendar-cita', {
                                tramite_id: this.tramiteSeleccionado.id,
                                fecha_cita: this.fechaSeleccionada,
                                hora_cita: this.horaSeleccionada,
                                ...this.datosPersonales
                            });

                            if (response.data.success) {
                                this.citaCreada = response.data.cita;
                                this.step = 4;
                                window.scrollTo({
                                    top: 0,
                                    behavior: 'smooth'
                                });

                                // Confetti effect
                                if (typeof confetti !== 'undefined') {
                                    confetti({
                                        particleCount: 100,
                                        spread: 70,
                                        origin: {
                                            y: 0.6
                                        }
                                    });
                                }

                                Swal.fire({
                                    title: '¡Éxito!',
                                    text: 'Su cita ha sido agendada correctamente',
                                    icon: 'success',
                                    timer: 3000,
                                    showConfirmButton: false,
                                    toast: true,
                                    position: 'top-end'
                                });
                            }
                        } catch (error) {
                            if (error.response?.data?.errors) {
                                const errores = Object.values(error.response.data.errors).flat().join('\n');
                                Swal.fire('Error de Validación', errores, 'error');
                            } else {
                                Swal.fire('Error', error.response?.data?.message || 'Ocurrió un error al agendar la cita',
                                    'error');
                            }
                        } finally {
                            this.enviando = false;
                        }
                    },

                    // Función mejorada para cargar horas disponibles
                    async cargarHorasDisponibles() {
                        if (!this.fechaSeleccionada || !this.tramiteSeleccionado) return;

                        try {
                            const response = await axios.get(
                                `/tramites/${this.tramiteSeleccionado.id}/horas/${this.fechaSeleccionada}`);
                            this.horasDisponibles = response.data.horas || response.data; // Mantener compatibilidad
                            this.horaSeleccionada = '';

                            // Mostrar información sobre horario de almuerzo si está disponible
                            if (response.data.horario_almuerzo) {
                                this.mostrarInfoAlmuerzo(response.data.horario_almuerzo);
                            }
                        } catch (error) {
                            this.horasDisponibles = [];
                            Swal.fire('Error', 'No se pudieron cargar los horarios disponibles', 'error');
                        }
                    },

                    descargarPDF() {
                        if (this.citaCreada?.numero_cita) {
                            window.open(`/cita/${this.citaCreada.numero_cita}/pdf`, '_blank');
                        }
                    },

                    formatearFecha(fecha) {
                        if (!fecha) return '';
                        return new Date(fecha).toLocaleDateString('es-CO', {
                            weekday: 'long',
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        });
                    },

                    reiniciar() {
                        // Smooth animation before reset
                        Swal.fire({
                            title: '¿Agendar nueva cita?',
                            text: 'Se reiniciará el proceso de agendamiento',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Sí, continuar',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.step = 1;
                                this.secretariaAbierta = null;
                                this.areaAbierta = null;
                                this.tramiteSeleccionado = null;
                                this.fechaSeleccionada = '';
                                this.horaSeleccionada = '';
                                this.horasDisponibles = [];
                                this.citaCreada = null;
                                this.datosPersonales = {
                                    tipo_documento: '',
                                    numero_documento: '',
                                    nombres: '',
                                    apellidos: '',
                                    email: '',
                                    telefono: '',
                                    direccion: '',
                                    acepta_tratamiento_datos: false
                                };
                                window.scrollTo({
                                    top: 0,
                                    behavior: 'smooth'
                                });
                            }
                        });
                    }
                }
            }
        </script>
    @endpush
