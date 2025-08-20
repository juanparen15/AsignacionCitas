<?php
// app/Filament/Widgets/CitasPorArea.php

namespace App\Filament\Widgets;

use App\Models\Cita;
use App\Models\Area;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CitasPorArea extends ChartWidget
{
    protected static ?string $heading = 'Distribución de Citas por Área';
    protected static ?int $sort = 4;
    protected static ?string $pollingInterval = '120s';
    protected static ?string $maxHeight = '350px';
    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 1,
    ];

    public ?string $filter = 'mes_actual';

    protected function getFilters(): ?array
    {
        return [
            'hoy' => 'Hoy',
            'semana_actual' => 'Esta semana',
            'mes_actual' => 'Este mes',
            'trimestre_actual' => 'Este trimestre',
        ];
    }

    protected function getData(): array
    {
        $user = Auth::user();
        
        // Si el usuario solo puede ver su área, mostrar solo esa área
        if (!$user->canViewAllCitas() && $user->area_id) {
            return $this->getAreaSpecificData($user->area_id);
        }
        
        $dateRange = $this->getDateRange();
        
        $data = Cita::query()
            ->join('tramites', 'citas.tramite_id', '=', 'tramites.id')
            ->join('areas', 'tramites.area_id', '=', 'areas.id')
            ->whereBetween('citas.fecha_cita', $dateRange)
            ->select('areas.nombre', DB::raw('count(*) as total'))
            ->groupBy('areas.id', 'areas.nombre')
            ->orderBy('total', 'desc')
            ->limit(10) // Top 10 áreas
            ->get();

        $labels = $data->pluck('nombre')->toArray();
        $values = $data->pluck('total')->toArray();
        
        // Generar colores dinámicos
        $colors = $this->generateColors(count($labels));

        return [
            'datasets' => [
                [
                    'data' => $values,
                    'backgroundColor' => $colors,
                    'borderColor' => $colors,
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    private function getAreaSpecificData($areaId): array
    {
        $area = Area::find($areaId);
        $dateRange = $this->getDateRange();
        
        $estadosCitas = Cita::query()
            ->whereHas('tramite', fn($q) => $q->where('area_id', $areaId))
            ->whereBetween('fecha_cita', $dateRange)
            ->select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->get();

        $labels = [];
        $values = [];
        $colors = [];
        
        $colorMap = [
            'programada' => '#f59e0b',
            'confirmada' => '#3b82f6',
            'en_proceso' => '#8b5cf6',
            'atendida' => '#10b981',
            'cancelada' => '#ef4444',
            'no_asistio' => '#6b7280',
        ];

        foreach ($estadosCitas as $estado) {
            $labels[] = Cita::ESTADOS[$estado->estado] ?? $estado->estado;
            $values[] = $estado->total;
            $colors[] = $colorMap[$estado->estado] ?? '#6b7280';
        }

        return [
            'datasets' => [
                [
                    'data' => $values,
                    'backgroundColor' => $colors,
                    'borderColor' => $colors,
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    private function getDateRange(): array
    {
        return match ($this->filter) {
            'hoy' => [now()->startOfDay(), now()->endOfDay()],
            'semana_actual' => [now()->startOfWeek(), now()->endOfWeek()],
            'trimestre_actual' => [now()->startOfQuarter(), now()->endOfQuarter()],
            default => [now()->startOfMonth(), now()->endOfMonth()],
        };
    }

    private function generateColors(int $count): array
    {
        $baseColors = [
            '#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6',
            '#ec4899', '#14b8a6', '#f97316', '#6366f1', '#84cc16',
        ];
        
        $colors = [];
        for ($i = 0; $i < $count; $i++) {
            $colors[] = $baseColors[$i % count($baseColors)];
        }
        
        return $colors;
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'right',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            const label = context.label || "";
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return label + ": " + value + " (" + percentage + "%)";
                        }',
                    ],
                ],
            ],
        ];
    }

    // protected function getHeading(): string
    // {
    //     $user = Auth::user();
        
    //     if (!$user->canViewAllCitas() && $user->area_id) {
    //         $area = Area::find($user->area_id);
    //         return "Estados de Citas - {$area->nombre}";
    //     }
        
    //     return 'Distribución de Citas por Área';
    // }

    public static function canView(): bool
    {
        return Auth::user()->hasPermission('view_analytics') || 
               Auth::user()->hasPermission('view_area_citas');
    }
}