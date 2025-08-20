<!-- resources/views/agendamiento/index.blade.php -->
@extends('layouts.app')

@section('title', 'Agendar Cita')

@section('content')
<div x-data="agendamientoApp()" class="max-w-4xl mx-auto">
    <!-- Progress Steps -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="flex items-center">
                    <div 
                        class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-medium transition-colors"
                        :class="step >= 1 ? (step > 1 ? 'step-completed' : 'step-active') : 'step-inactive'"
                    >
                        <i class="fas fa-building" x-show="step === 1"></i>
                        <i class="fas fa-check" x-show="step > 1"></i>
                    </div>
                    <span class="ml-2 text-sm font-medium" :class="step >= 1 ? 'text-gray-900' : 'text-gray-500'">
                        Seleccionar Trámite
                    </span>
                </div>
                
                <div class="flex-1 h-1 bg-gray-200 mx-4">
                    <div 
                        class="h-full bg-blue-600 transition-all duration-300"
                        :style="{ width: step >= 2 ? '100%' : '0%' }"
                    ></div>
                </div>
                
                <div class="flex items-center">
                    <div 
                        class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-medium transition-colors"
                        :class="step >= 2 ? (step > 2 ? 'step-completed' : 'step-active') : 'step-inactive'"
                    >
                        <i class="fas fa-calendar" x-show="step === 2"></i>
                        <i class="fas fa-check" x-show="step > 2"></i>
                    </div>
                    <span class="ml-2 text-sm font-medium" :class="step >= 2 ? 'text-gray-900' : 'text-gray-500'">
                        Fecha y Hora
                    </span>
                </div>
                
                <div class="flex-1 h-1 bg-gray-200 mx-4">
                    <div 
                        class="h-full bg-blue-600 transition-all duration-300"
                        :style="{ width: step >= 3 ? '100%' : '0%' }"
                    ></div>
                </div>
                
                <div class="flex items-center">
                    <div 
                        class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-medium transition-colors"
                        :class="step >= 3 ? (step > 3 ? 'step-completed' : 'step-active') : 'step-inactive'"
                    >
                        <i class="fas fa-user" x-show="step === 3"></i>
                        <i class="fas fa-check" x-show="step > 3"></i>
                    </div>
                    <span class="ml-2 text-sm font-medium" :class="step >= 3 ? 'text-gray-900' : 'text-gray-500'">
                        Datos Personales
                    </span>
                </div>
                
                <div class="flex-1 h-1 bg-gray-200 mx-4">
                    <div 
                        class="h-full bg-blue-600 transition-all duration-300"
                        :style="{ width: step >= 4 ? '100%' : '0%' }"
                    ></div>
                </div>
                
                <div class="flex items-center">
                    <div 
                        class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-medium transition-colors"
                        :class="step >= 4 ? 'step-active' : 'step-inactive'"
                    >
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <span class="ml-2 text-sm font-medium" :class="step >= 4 ? 'text-gray-900' : 'text-gray-500'">
                        Confirmación
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 1: Seleccionar Trámite -->
    <div x-show="step === 1" class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-6 text-gray-800">Paso 1: Seleccione el trámite que desea realizar</h2>
        
        <!-- Secretarías -->
        <div class="space-y-4">
            <template x-for="secretaria in secretarias" :key="secretaria.id">
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button 
                        @click="toggleSecretaria(secretaria.id)"
                        class="w-full px-6 py-4 bg-gray-50 hover:bg-gray-100 flex items-center justify-between transition-colors"
                    >
                        <div class="flex items-center">
                            <i class="fas fa-building text-blue-600 mr-3"></i>
                            <span class="font-medium text-gray-800" x-text="secretaria.nombre"></span>
                        </div>
                        <i 
                            class="fas fa-chevron-down transition-transform"
                            :class="secretariaAbierta === secretaria.id ? 'rotate-180' : ''"
                        ></i>
                    </button>
                    
                    <div 
                        x-show="secretariaAbierta === secretaria.id"
                        x-transition
                        class="border-t border-gray-200"
                    >
                        <!-- Áreas -->
                        <template x-for="area in secretaria.areas_activas" :key="area.id">
                            <div class="border-b border-gray-100 last:border-b-0">
                                <button 
                                    @click="toggleArea(area.id)"
                                    class="w-full px-8 py-3 bg-white hover:bg-gray-50 flex items-center justify-between transition-colors"
                                >
                                    <div class="flex items-center">
                                        <i class="fas fa-sitemap text-green-600 mr-3"></i>
                                        <span class="text-gray-700" x-text="area.nombre"></span>
                                    </div>
                                    <i 
                                        class="fas fa-chevron-down transition-transform"
                                        :class="areaAbierta === area.id ? 'rotate-180' : ''"
                                    ></i>
                                </button>
                                
                                <div 
                                    x-show="areaAbierta === area.id"
                                    x-transition
                                    class="bg-gray-50"
                                >
                                    <!-- Trámites -->
                                    <template x-for="tramite in area.tramites_activos" :key="tramite.id">
                                        <button 
                                            @click="seleccionarTramite(tramite)"
                                            class="w-full px-12 py-4 text-left hover:bg-blue-50 border-b border-gray-200 last:border-b-0 transition-colors group"
                                        >
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex items-center">
                                                        <i class="fas fa-file-alt text-purple-600 mr-3"></i>
                                                        <h4 class="font-medium text-gray-800 group-hover:text-blue-700" x-text="tramite.nombre"></h4>
                                                    </div>
                                                    <p class="text-sm text-gray-600 mt-1" x-text="tramite.descripcion"></p>
                                                    <div class="flex items-center mt-2 space-x-4">
                                                        <span 
                                                            class="text-sm px-2 py-1 rounded"
                                                            :class="tramite.es_gratuito ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'"
                                                            x-text="tramite.es_gratuito ? 'Gratuito' : ' + new Intl.NumberFormat('es-CO').format(tramite.costo)"
                                                        ></span>
                                                        <span class="text-sm text-gray-500">
                                                            <i class="fas fa-clock mr-1"></i>
                                                            <span x-text="tramite.duracion_minutos"></span> min
                                                        </span>
                                                    </div>
                                                </div>
                                                <i class="fas fa-arrow-right text-blue-600 opacity-0 group-hover:opacity-100 transition-opacity"></i>
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
    <div x-show="step === 2" class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-6 text-gray-800">Paso 2: Seleccione fecha y hora para su cita</h2>
        
        <div class="grid md:grid-cols-2 gap-6">
            <!-- Información del trámite seleccionado -->
            <div class="bg-blue-50 rounded-lg p-4 mb-6 md:col-span-2">
                <h3 class="font-semibold text-blue-800 mb-2">Trámite Seleccionado:</h3>
                <p class="text-blue-700" x-text="tramiteSeleccionado?.nombre"></p>
                <p class="text-sm text-blue-600 mt-1" x-text="tramiteSeleccionado?.area?.secretaria?.nombre + ' - ' + tramiteSeleccionado?.area?.nombre"></p>
            </div>
            
            <!-- Selección de Fecha -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Seleccione la Fecha</label>
                <input 
                    type="date" 
                    x-model="fechaSeleccionada"
                    @change="cargarHorasDisponibles()"
                    :min="fechaMinima"
                    :max="fechaMaxima"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                <p class="text-xs text-gray-500 mt-1">
                    Disponible desde <span x-text="fechaMinima"></span> hasta <span x-text="fechaMaxima"></span>
                </p>
            </div>
            
            <!-- Selección de Hora -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Seleccione la Hora</label>
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    <template x-for="hora in horasDisponibles" :key="hora.hora">
                        <button 
                            @click="horaSeleccionada = hora.hora"
                            class="w-full px-4 py-3 border rounded-md text-left transition-colors"
                            :class="horaSeleccionada === hora.hora ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-200 hover:border-gray-300'"
                        >
                            <div class="flex items-center justify-between">
                                <span class="font-medium" x-text="hora.hora"></span>
                                <span class="text-sm text-gray-500">
                                    <span x-text="hora.disponibles"></span>/<span x-text="hora.total"></span> disponibles
                                </span>
                            </div>
                        </button>
                    </template>
                    <div x-show="horasDisponibles.length === 0 && fechaSeleccionada" class="text-center py-8 text-gray-500">
                        <i class="fas fa-calendar-times text-4xl mb-2"></i>
                        <p>No hay horarios disponibles para esta fecha</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="flex justify-between mt-6">
            <button 
                @click="step = 1"
                class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors"
            >
                <i class="fas fa-arrow-left mr-2"></i>
                Anterior
            </button>
            <button 
                @click="avanzarPaso()"
                :disabled="!fechaSeleccionada || !horaSeleccionada"
                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors"
            >
                Siguiente
                <i class="fas fa-arrow-right ml-2"></i>
            </button>
        </div>
    </div>

    <!-- Step 3: Datos Personales -->
    <div x-show="step === 3" class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-6 text-gray-800">Paso 3: Ingrese sus datos personales</h2>
        
        <form @submit.prevent="agendarCita()" class="space-y-6">
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Tipo de Documento -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tipo de Documento <span class="text-red-500">*</span>
                    </label>
                    <select 
                        x-model="datosPersonales.tipo_documento"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required
                    >
                        <option value="">Seleccione...</option>
                        <option value="CC">Cédula de Ciudadanía</option>
                        <option value="CE">Cédula de Extranjería</option>
                        <option value="PA">Pasaporte</option>
                        <option value="TI">Tarjeta de Identidad</option>
                        <option value="RC">Registro Civil</option>
                    </select>
                </div>
                
                <!-- Número de Documento -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Número de Documento <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        x-model="datosPersonales.numero_documento"
                        pattern="[0-9]+"
                        maxlength="20"
                        placeholder="Solo números"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required
                    >
                </div>
                
                <!-- Nombres -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nombres <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        x-model="datosPersonales.nombres"
                        pattern="[a-zA-ZÀ-ÿ\s]+"
                        maxlength="100"
                        placeholder="Solo letras y espacios"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required
                    >
                </div>
                
                <!-- Apellidos -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Apellidos <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        x-model="datosPersonales.apellidos"
                        pattern="[a-zA-ZÀ-ÿ\s]+"
                        maxlength="100"
                        placeholder="Solo letras y espacios"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required
                    >
                </div>
                
                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Correo Electrónico <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="email" 
                        x-model="datosPersonales.email"
                        maxlength="150"
                        placeholder="ejemplo@correo.com"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required
                    >
                </div>
                
                <!-- Teléfono -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Teléfono <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        x-model="datosPersonales.telefono"
                        pattern="[0-9+\-\s]+"
                        maxlength="15"
                        placeholder="3001234567"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required
                    >
                </div>
                
                <!-- Dirección (Opcional) -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Dirección (Opcional)
                    </label>
                    <input 
                        type="text" 
                        x-model="datosPersonales.direccion"
                        maxlength="200"
                        placeholder="Calle 123 # 45-67"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>
            </div>
            
            <!-- Tratamiento de Datos -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <div class="flex items-start">
                    <input 
                        type="checkbox" 
                        x-model="datosPersonales.acepta_tratamiento_datos"
                        id="tratamiento_datos"
                        class="mt-1 mr-3"
                        required
                    >
                    <label for="tratamiento_datos" class="text-sm text-gray-700">
                        <span class="font-medium">Acepto el tratamiento de mis datos personales</span> <span class="text-red-500">*</span>
                        <br>
                        <span class="text-xs">
                            Al marcar esta casilla, autorizo el tratamiento de mis datos personales conforme a la 
                            <a href="#" class="text-blue-600 hover:underline">Política de Privacidad</a> 
                            para los fines relacionados con el agendamiento y gestión de citas.
                        </span>
                    </label>
                </div>
            </div>
            
            <div class="flex justify-between">
                <button 
                    type="button"
                    @click="step = 2"
                    class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors"
                >
                    <i class="fas fa-arrow-left mr-2"></i>
                    Anterior
                </button>
                <button 
                    type="submit"
                    :disabled="!formularioValido() || enviando"
                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors"
                >
                    <span x-show="!enviando">
                        Agendar Cita
                        <i class="fas fa-check ml-2"></i>
                    </span>
                    <span x-show="enviando" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Procesando...
                    </span>
                </button>
            </div>
        </form>
    </div>

    <!-- Step 4: Confirmación -->
    <div x-show="step === 4" class="bg-white rounded-lg shadow-md p-6">
        <div class="text-center">
            <div class="mb-6">
                <i class="fas fa-check-circle text-6xl text-green-500"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-4">¡Cita Agendada Exitosamente!</h2>
            
            <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
                <div class="text-left space-y-3">
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-700">Número de Cita:</span>
                        <span class="font-bold text-green-700" x-text="citaCreada?.numero_cita"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-700">Ciudadano:</span>
                        <span x-text="citaCreada?.nombres + ' ' + citaCreada?.apellidos"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-700">Trámite:</span>
                        <span x-text="citaCreada?.tramite?.nombre"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-700">Fecha:</span>
                        <span x-text="formatearFecha(citaCreada?.fecha_cita)"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-700">Hora:</span>
                        <span x-text="citaCreada?.hora_cita?.substring(0, 5)"></span>
                    </div>
                </div>
            </div>
            
            <div class="space-y-4">
                <button 
                    @click="descargarPDF()"
                    class="w-full bg-red-600 text-white py-3 px-6 rounded-md hover:bg-red-700 transition-colors"
                >
                    <i class="fas fa-file-pdf mr-2"></i>
                    Descargar Comprobante PDF
                </button>
                
                <button 
                    @click="reiniciar()"
                    class="w-full bg-blue-600 text-white py-3 px-6 rounded-md hover:bg-blue-700 transition-colors"
                >
                    <i class="fas fa-plus mr-2"></i>
                    Agendar Nueva Cita
                </button>
            </div>
            
            <div class="mt-6 text-sm text-gray-600">
                <p><strong>Importante:</strong> Guarde este número de cita para futuras consultas.</p>
                <p>Llegue 15 minutos antes de su cita con los documentos requeridos.</p>
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
            } catch (error) {
                Swal.fire('Error', 'No se pudo cargar la configuración del trámite', 'error');
            }
        },

        async cargarHorasDisponibles() {
            if (!this.fechaSeleccionada || !this.tramiteSeleccionado) return;
            
            try {
                const response = await axios.get(`/tramites/${this.tramiteSeleccionado.id}/horas/${this.fechaSeleccionada}`);
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
                    
                    Swal.fire({
                        title: '¡Éxito!',
                        text: 'Su cita ha sido agendada correctamente',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            } catch (error) {
                if (error.response?.data?.errors) {
                    const errores = Object.values(error.response.data.errors).flat().join('\n');
                    Swal.fire('Error de Validación', errores, 'error');
                } else {
                    Swal.fire('Error', error.response?.data?.message || 'Ocurrió un error al agendar la cita', 'error');
                }
            } finally {
                this.enviando = false;
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
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        },

        reiniciar() {
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
        }
    }
}
</script>
@endpush