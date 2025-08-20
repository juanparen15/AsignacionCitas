<?php

// app/Filament/Widgets/CitasRecientesWidget.php
namespace App\Filament\Widgets;

use App\Models\Cita;
use Filament\Forms\Components\Textarea;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;

// class CitasRecientesWidget extends BaseWidget
// {
//     protected int | string | array $columnSpan = 'full';
//     protected static ?string $heading = 'Citas Recientes';
//     protected static ?int $sort = 4;

//     public function table(Table $table): Table
//     {
//         return $table
//             ->query(
//                 Cita::with(['tramite.area.secretaria'])
//                     ->latest('created_at')
//                     ->limit(10)
//             )
//             ->columns([
//                 Tables\Columns\TextColumn::make('numero_cita')
//                     ->label('No. Cita')
//                     ->searchable()
//                     ->copyable()
//                     ->weight('medium'),

//                 Tables\Columns\TextColumn::make('fecha_hora_cita')
//                     ->label('Fecha y Hora')
//                     ->dateTime('d/m/Y H:i')
//                     ->sortable()
//                     ->color(
//                         fn(Cita $record): string =>
//                         $record->fecha_hora_cita->isPast() && $record->estado === 'programada' ? 'danger' : 'primary'
//                     ),

//                 Tables\Columns\TextColumn::make('tramite.nombre')
//                     ->label('Trámite')
//                     ->searchable()
//                     ->wrap()
//                     ->limit(30),

//                 Tables\Columns\TextColumn::make('nombres')
//                     ->label('Cliente')
//                     ->searchable()
//                     ->formatStateUsing(
//                         fn(Cita $record): string =>
//                         $record->nombres . ' ' . $record->apellidos
//                     ),

//                 Tables\Columns\TextColumn::make('email')
//                     ->label('Email')
//                     ->searchable()
//                     ->toggleable()
//                     ->copyable(),

//                 Tables\Columns\TextColumn::make('telefono')
//                     ->label('Teléfono')
//                     ->searchable()
//                     ->toggleable()
//                     ->copyable(),

//                 Tables\Columns\BadgeColumn::make('estado')
//                     ->label('Estado')
//                     ->colors([
//                         'warning' => 'programada',
//                         'success' => 'confirmada',
//                         'info' => 'en_proceso',
//                         'primary' => 'atendida',
//                         'danger' => 'cancelada',
//                         'secondary' => 'no_asistio',
//                     ])
//                     ->formatStateUsing(fn(string $state): string => match ($state) {
//                         'programada' => 'Programada',
//                         'confirmada' => 'Confirmada',
//                         'en_proceso' => 'En Proceso',
//                         'atendida' => 'Atendida',
//                         'cancelada' => 'Cancelada',
//                         'no_asistio' => 'No Asistió',
//                         default => ucfirst($state),
//                     }),

//                 Tables\Columns\TextColumn::make('tramite.area.secretaria.nombre')
//                     ->label('Secretaría')
//                     ->toggleable(isToggledHiddenByDefault: true),

//                 Tables\Columns\TextColumn::make('created_at')
//                     ->label('Creada')
//                     ->since()
//                     ->toggleable(isToggledHiddenByDefault: true),
//             ])
//             ->actions([
//                 Action::make('view')
//                     ->label('Ver')
//                     ->icon('heroicon-m-eye')
//                     ->color('primary')
//                     ->url(
//                         fn(Cita $record): string =>
//                         route('filament.admin.resources.citas.edit', $record)
//                     ),

//                 Action::make('confirm')
//                     ->label('Confirmar')
//                     ->icon('heroicon-m-check')
//                     ->color('success')
//                     ->action(function (Cita $record) {
//                         $record->update(['estado' => 'confirmada']);
//                         $this->notify('success', 'Cita confirmada exitosamente');
//                     })
//                     ->visible(fn(Cita $record): bool => $record->estado === 'programada')
//                     ->requiresConfirmation(),

//                 Action::make('process')
//                     ->label('En Proceso')
//                     ->icon('heroicon-m-arrow-path')
//                     ->color('info')
//                     ->action(function (Cita $record) {
//                         $record->update(['estado' => 'en_proceso']);
//                         $this->notify('success', 'Cita marcada como en proceso');
//                     })
//                     ->visible(fn(Cita $record): bool => $record->estado === 'confirmada')
//                     ->requiresConfirmation(),

//                 Action::make('complete')
//                     ->label('Atender')
//                     ->icon('heroicon-m-check-circle')
//                     ->color('primary')
//                     ->action(function (Cita $record) {
//                         $record->update(['estado' => 'atendida']);
//                         $this->notify('success', 'Cita marcada como atendida');
//                     })
//                     ->visible(fn(Cita $record): bool => $record->estado === 'en_proceso')
//                     ->requiresConfirmation(),

//                 Action::make('cancel')
//                     ->label('Cancelar')
//                     ->icon('heroicon-m-x-mark')
//                     ->color('danger')
//                     ->form([
//                         Textarea::make('motivo_cancelacion')
//                             ->label('Motivo de Cancelación')
//                             ->required()
//                             ->placeholder('Explica el motivo de la cancelación...')
//                     ])
//                     ->action(function (Cita $record, array $data) {
//                         $record->update([
//                             'estado' => 'cancelada',
//                             'observaciones' => ($record->observaciones ? $record->observaciones . '\n\n' : '') .
//                                 'CANCELADA: ' . $data['motivo_cancelacion']
//                         ]);
//                         $this->notify('success', 'Cita cancelada exitosamente');
//                     })
//                     ->visible(
//                         fn(Cita $record): bool =>
//                         in_array($record->estado, ['programada', 'confirmada'])
//                     ),
//             ])
//             ->poll('30s'); // Auto-refresh cada 30 segundos
//     }

//     private function notify(string $type, string $message): void
//     {
//         $this->dispatch('notify', [
//             'type' => $type,
//             'message' => $message
//         ]);
//     }

//     public static function canView(): bool
//     {
//         return Auth::user()->hasPermission('view_area_citas') ||
//             Auth::user()->hasPermission('view_all_citas');
//     }
// }
