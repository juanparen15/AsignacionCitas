<?php
// app/Filament/Widgets/CitasChart.php

namespace App\Filament\Widgets;

use App\Models\Cita;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CitasChart extends ChartWidget
{
    protected static ?string $heading = 'Citas por Día';
    protected static ?int $sort = 3;
    protected static ?string $pollingInterval = '60s';
    // protected static ?string $maxHeight = '300px';
    // protected int | string | array $columnSpan = 'full';
    protected static ?string $maxHeight = '350px';
    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 1,
    ];

    public ?string $filter = '7_days';

    protected function getFilters(): ?array
    {
        return [
            '7_days' => 'Últimos 7 días',
            '15_days' => 'Últimos 15 días',
            '30_days' => 'Último mes',
            '90_days' => 'Últimos 3 meses',
        ];
    }

    protected function getData(): array
    {
        $user = Auth::user();
        $citasQuery = $user->getAccessibleCitasQuery();

        $days = match ($this->filter) {
            '15_days' => 15,
            '30_days' => 30,
            '90_days' => 90,
            default => 7,
        };

        $labels = [];
        $programadas = [];
        $confirmadas = [];
        $atendidas = [];
        $canceladas = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format($days > 30 ? 'M j' : 'D j');

            $dayQuery = $citasQuery->clone()->whereDate('fecha_cita', $date);

            $programadas[] = $dayQuery->clone()->where('estado', 'programada')->count();
            $confirmadas[] = $dayQuery->clone()->where('estado', 'confirmada')->count();
            $atendidas[] = $dayQuery->clone()->where('estado', 'atendida')->count();
            $canceladas[] = $dayQuery->clone()->where('estado', 'cancelada')->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Programadas',
                    'data' => $programadas,
                    'backgroundColor' => 'rgb(249, 115, 22)',
                    'borderColor' => 'rgb(249, 115, 22)',
                    'fill' => false,
                ],
                [
                    'label' => 'Confirmadas',
                    'data' => $confirmadas,
                    'backgroundColor' => 'rgb(59, 130, 246)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'fill' => false,
                ],
                [
                    'label' => 'Atendidas',
                    'data' => $atendidas,
                    'backgroundColor' => 'rgb(34, 197, 94)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'fill' => false,
                ],
                [
                    'label' => 'Canceladas',
                    'data' => $canceladas,
                    'backgroundColor' => 'rgb(239, 68, 68)',
                    'borderColor' => 'rgb(239, 68, 68)',
                    'fill' => false,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'left',
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
            'scales' => [
                'x' => [
                    'display' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Fecha',
                    ],
                ],
                'y' => [
                    'display' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Número de Citas',
                    ],
                    'beginAtZero' => true,
                ],
            ],
            'interaction' => [
                'mode' => 'nearest',
                'axis' => 'x',
                'intersect' => false,
            ],
        ];
    }

    public static function canView(): bool
    {
        return Auth::user()->hasPermission('view_analytics') ||
            Auth::user()->hasPermission('view_area_citas');
    }
}
