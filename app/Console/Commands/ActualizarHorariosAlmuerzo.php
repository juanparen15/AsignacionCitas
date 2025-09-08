<?php
// app/Console/Commands/ActualizarHorariosAlmuerzo.php
// php artisan make:command ActualizarHorariosAlmuerzo

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ConfiguracionTramite;
use App\Models\Cita;
use Carbon\Carbon;

class ActualizarHorariosAlmuerzo extends Command
{
    protected $signature = 'citas:actualizar-horarios-almuerzo {--verificar-solo : Solo verificar citas existentes sin modificar}';
    protected $description = 'Actualiza las configuraciones para excluir horario de almuerzo y verifica citas existentes';

    public function handle()
    {
        $this->info('🍽️  Iniciando actualización de horarios de almuerzo...');
        $this->newLine();

        $verificarSolo = $this->option('verificar-solo');

        // 1. Verificar citas existentes en horario de almuerzo
        $this->verificarCitasEnAlmuerzo();

        if (!$verificarSolo) {
            // 2. Actualizar configuraciones existentes
            $this->actualizarConfiguraciones();
        }

        // 3. Mostrar resumen
        $this->mostrarResumen();

        $this->newLine();
        $this->info('✅ Proceso completado exitosamente.');
    }

    private function verificarCitasEnAlmuerzo()
    {
        $this->info('🔍 Verificando citas existentes en horario de almuerzo...');
        
        $horaInicioAlmuerzo = ConfiguracionTramite::HORA_INICIO_ALMUERZO;
        $horaFinAlmuerzo = ConfiguracionTramite::HORA_FIN_ALMUERZO;

        $citasEnAlmuerzo = Cita::whereTime('hora_cita', '>=', $horaInicioAlmuerzo)
            ->whereTime('hora_cita', '<', $horaFinAlmuerzo)
            ->whereIn('estado', ['programada', 'confirmada'])
            ->with(['tramite.area.secretaria'])
            ->get();

        if ($citasEnAlmuerzo->count() > 0) {
            $this->warn("⚠️  Se encontraron {$citasEnAlmuerzo->count()} citas programadas en horario de almuerzo:");
            $this->newLine();

            $headers = ['Número Cita', 'Fecha', 'Hora', 'Ciudadano', 'Trámite', 'Estado'];
            $rows = [];

            foreach ($citasEnAlmuerzo as $cita) {
                $rows[] = [
                    $cita->numero_cita,
                    $cita->fecha_cita->format('d/m/Y'),
                    $cita->hora_cita->format('H:i'),
                    $cita->nombres . ' ' . $cita->apellidos,
                    $cita->tramite->nombre,
                    $cita->estado_label
                ];
            }

            $this->table($headers, $rows);

            if ($this->confirm('¿Desea reprogramar automáticamente estas citas?')) {
                $this->reprogramarCitasAlmuerzo($citasEnAlmuerzo);
            } else {
                $this->info('💡 Recomendación: Contacte a estos ciudadanos para reprogramar sus citas.');
            }
        } else {
            $this->info('✅ No se encontraron citas en horario de almuerzo.');
        }

        $this->newLine();
    }

    private function reprogramarCitasAlmuerzo($citas)
    {
        $this->info('🔄 Reprogramando citas...');
        $reprogramadas = 0;

        foreach ($citas as $cita) {
            $nuevaHora = $this->buscarHoraDisponible($cita);
            
            if ($nuevaHora) {
                $horaAnterior = $cita->hora_cita->format('H:i');
                $cita->update([
                    'hora_cita' => $nuevaHora,
                    'observaciones' => ($cita->observaciones ? $cita->observaciones . "\n" : '') . 
                                     "Reprogramada automáticamente de {$horaAnterior} a {$nuevaHora} por horario de almuerzo."
                ]);
                
                $this->line("  ✅ Cita {$cita->numero_cita}: {$horaAnterior} → {$nuevaHora}");
                $reprogramadas++;
            } else {
                $this->line("  ❌ No se pudo reprogramar automáticamente la cita {$cita->numero_cita}");
            }
        }

        $this->info("📊 Resultado: {$reprogramadas} de {$citas->count()} citas reprogramadas automáticamente.");
    }

    private function buscarHoraDisponible($cita)
    {
        $configuracion = $cita->tramite->configuracion;
        if (!$configuracion) return null;

        $fecha = $cita->fecha_cita;
        $horasDisponibles = $configuracion->getHorasDisponibles($fecha);

        foreach ($horasDisponibles as $hora) {
            $citasEnHora = Cita::where('tramite_id', $cita->tramite_id)
                ->whereDate('fecha_cita', $fecha)
                ->whereTime('hora_cita', $hora)
                ->whereIn('estado', ['programada', 'confirmada'])
                ->count();

            if ($citasEnHora < $configuracion->citas_por_hora) {
                return $hora;
            }
        }

        return null;
    }

    private function actualizarConfiguraciones()
    {
        $this->info('⚙️  Actualizando configuraciones de trámites...');

        $configuraciones = ConfiguracionTramite::all();
        $actualizadas = 0;

        foreach ($configuraciones as $config) {
            // Verificar si la configuración actual incluye horario de almuerzo
            $horaInicio = Carbon::parse($config->hora_inicio);
            $horaFin = Carbon::parse($config->hora_fin);
            $horaInicioAlmuerzo = Carbon::parse(ConfiguracionTramite::HORA_INICIO_ALMUERZO);
            $horaFinAlmuerzo = Carbon::parse(ConfiguracionTramite::HORA_FIN_ALMUERZO);

            // Si el horario incluye las horas de almuerzo, registrar información
            if ($horaInicio->lt($horaFinAlmuerzo) && $horaFin->gt($horaInicioAlmuerzo)) {
                $this->line("  📝 Configuración del trámite '{$config->tramite->nombre}' actualizada para excluir horario de almuerzo");
                $actualizadas++;
            }
        }

        $this->info("✅ Se han actualizado {$actualizadas} configuraciones para excluir el horario de almuerzo.");
        $this->newLine();
    }

    private function mostrarResumen()
    {
        $this->info('📋 RESUMEN DE CONFIGURACIÓN:');
        $this->newLine();

        $horarioAlmuerzo = ConfiguracionTramite::getHorarioAlmuerzo();
        
        $this->line("🕐 Horario de almuerzo: {$horarioAlmuerzo['inicio']} - {$horarioAlmuerzo['fin']}");
        $this->line("📝 Mensaje: {$horarioAlmuerzo['mensaje']}");
        $this->newLine();

        // Estadísticas generales
        $totalConfiguraciones = ConfiguracionTramite::count();
        $totalCitasFuturas = Cita::where('fecha_cita', '>=', now())
            ->whereIn('estado', ['programada', 'confirmada'])
            ->count();

        $this->line("📊 Total de configuraciones de trámites: {$totalConfiguraciones}");
        $this->line("📅 Total de citas futuras programadas: {$totalCitasFuturas}");
        
        // Verificar citas futuras en horario de almuerzo
        $citasFuturasAlmuerzo = Cita::where('fecha_cita', '>=', now())
            ->whereTime('hora_cita', '>=', ConfiguracionTramite::HORA_INICIO_ALMUERZO)
            ->whereTime('hora_cita', '<', ConfiguracionTramite::HORA_FIN_ALMUERZO)
            ->whereIn('estado', ['programada', 'confirmada'])
            ->count();

        if ($citasFuturasAlmuerzo > 0) {
            $this->warn("⚠️  Aún hay {$citasFuturasAlmuerzo} citas futuras en horario de almuerzo que requieren atención.");
        } else {
            $this->info("✅ No hay citas futuras programadas en horario de almuerzo.");
        }
    }
}