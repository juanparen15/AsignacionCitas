<?php
// app/Filament/Widgets/StatsOverview.php

namespace App\Filament\Widgets;

use App\Models\Cita;
use App\Models\User;
use App\Models\Tramite;
use App\Models\Area;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $user = Auth::user();
        
        // Obtener query base según permisos del usuario
        $citasQuery = $user->getAccessibleCitasQuery();
        
        return [
            Stat::make('Total de Citas', $this->getTotalCitas($citasQuery))
                ->description('Todas las citas registradas')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary')
                ->chart($this->getCitasUltimos7Dias($citasQuery)),
            
            // Stat::make('Citas de Hoy', $this->getCitasHoy($citasQuery))
            //     ->description($this->getVariacionHoy($citasQuery))
            //     ->descriptionIcon($this->getIconoVariacionHoy($citasQuery))
            //     ->color($this->getColorVariacionHoy($citasQuery)),
            
            Stat::make('Citas Confirmadas', $this->getCitasConfirmadas($citasQuery))
                ->description('Listas para atender')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            
            Stat::make('Citas Pendientes', $this->getCitasPendientes($citasQuery))
                ->description('Esperando confirmación')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            
            // Solo mostrar estas estadísticas si tiene permisos de administrador
            ...$this->getAdminStats($user),
        ];
    }

    private function getTotalCitas($query): int
    {
        return $query->count();
    }

    private function getCitasHoy($query): int
    {
        return $query->whereDate('fecha_cita', today())->count();
    }

    private function getCitasConfirmadas($query): int
    {
        return $query->where('estado', 'confirmada')->count();
    }

    private function getCitasPendientes($query): int
    {
        return $query->where('estado', 'programada')->count();
    }

    private function getCitasUltimos7Dias($query): array
    {
        $datos = [];
        for ($i = 6; $i >= 0; $i--) {
            $fecha = Carbon::now()->subDays($i);
            $datos[] = $query->clone()->whereDate('fecha_cita', $fecha)->count();
        }
        return $datos;
    }

    private function getVariacionHoy($query): string
    {
        $hoy = $this->getCitasHoy($query);
        $ayer = $query->clone()->whereDate('fecha_cita', today())->count();
        
        if ($ayer == 0) {
            return $hoy > 0 ? 'Nuevas citas' : 'Sin citas';
        }
        
        $variacion = (($hoy - $ayer) / $ayer) * 100;
        $signo = $variacion > 0 ? '+' : '';
        
        return $signo . number_format($variacion, 1) . '% vs ayer';
    }

    private function getIconoVariacionHoy($query): string
    {
        $hoy = $this->getCitasHoy($query);
        $ayer = $query->clone()->whereDate('fecha_cita', today())->count();
        
        if ($hoy > $ayer) {
            return 'heroicon-m-arrow-trending-up';
        } elseif ($hoy < $ayer) {
            return 'heroicon-m-arrow-trending-down';
        }
        
        return 'heroicon-m-minus';
    }

    private function getColorVariacionHoy($query): string
    {
        $hoy = $this->getCitasHoy($query);
        $ayer = $query->clone()->whereDate('fecha_cita', today())->count();
        
        if ($hoy > $ayer) {
            return 'success';
        } elseif ($hoy < $ayer) {
            return 'danger';
        }
        
        return 'gray';
    }

    private function getAdminStats(User $user): array
    {
        if (!$user->hasPermission('view_all_citas') && !$user->hasRole('super_admin')) {
            return [];
        }

        return [
            Stat::make('Usuarios Activos', User::active()->count())
                ->description('En el sistema')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
            
            // Stat::make('Trámites Activos', Tramite::where('activo', true)->count())
            //     ->description('Disponibles para agendar')
            //     ->descriptionIcon('heroicon-m-document-text')
            //     ->color('primary'),
        ];
    }

    public static function canView(): bool
    {
        return Auth::user()->hasPermission('view_area_citas') || 
               Auth::user()->hasPermission('view_all_citas');
    }
}