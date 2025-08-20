<?php

// app/Filament/Widgets/CitasStatsWidget.php
namespace App\Filament\Widgets;

use App\Models\Cita;
use App\Models\Tramite;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class CitasStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $thisWeek = Carbon::now()->startOfWeek();
        $lastWeek = Carbon::now()->subWeek()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        // Estadísticas de hoy
        $citasHoy = Cita::whereDate('fecha_cita', $today)->count();
        $citasAyer = Cita::whereDate('fecha_cita', $yesterday)->count();
        $cambioHoy = $citasAyer > 0 ? (($citasHoy - $citasAyer) / $citasAyer) * 100 : 0;

        // Estadísticas de la semana
        $citasSemana = Cita::where('fecha_cita', '>=', $thisWeek)->count();
        $citasSemanaAnterior = Cita::whereBetween('fecha_cita', [
            $lastWeek, 
            $lastWeek->copy()->endOfWeek()
        ])->count();
        $cambioSemana = $citasSemanaAnterior > 0 ? (($citasSemana - $citasSemanaAnterior) / $citasSemanaAnterior) * 100 : 0;

        // Citas pendientes
        $citasPendientes = Cita::where('estado', 'programada')->count();

        // Tramites activos
        $tramitesActivos = Tramite::where('activo', true)->count();

        // Tendencia de los últimos 7 días
        $tendencia = [];
        for ($i = 6; $i >= 0; $i--) {
            $fecha = $today->copy()->subDays($i);
            $tendencia[] = Cita::whereDate('fecha_cita', $fecha)->count();
        }

        return [
            Stat::make('Citas Hoy', $citasHoy)
                ->description($cambioHoy >= 0 ? "+{$cambioHoy}% desde ayer" : "{$cambioHoy}% desde ayer")
                ->descriptionIcon($cambioHoy >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($cambioHoy >= 0 ? 'success' : 'danger')
                ->chart($tendencia),

            Stat::make('Esta Semana', $citasSemana)
                ->description($cambioSemana >= 0 ? "+{$cambioSemana}% vs semana anterior" : "{$cambioSemana}% vs semana anterior")
                ->descriptionIcon($cambioSemana >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($cambioSemana >= 0 ? 'success' : 'warning'),

            Stat::make('Programadas', $citasPendientes)
                ->description('Citas pendientes de atención')
                ->descriptionIcon('heroicon-m-clock')
                ->color($citasPendientes > 10 ? 'danger' : 'warning'),

            Stat::make('Confirmadas Hoy', Cita::whereDate('fecha_cita', $today)->where('estado', 'confirmada')->count())
                ->description('Citas confirmadas para hoy')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('En Proceso', Cita::where('estado', 'en_proceso')->count())
                ->description('Citas siendo atendidas')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('info'),

            Stat::make('Trámites Activos', $tramitesActivos)
                ->description('Total de trámites disponibles')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),
        ];
    }
}
