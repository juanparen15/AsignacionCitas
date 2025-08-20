<?php
// app/Filament/Resources/CitaResource.php - Método actualizado

namespace App\Filament\Resources;

use App\Filament\Resources\CitaResource\Pages;
use App\Models\Cita;
use App\Models\Tramite;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class CitaResource extends Resource
{
    protected static ?string $model = Cita::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Citas';
    protected static ?string $modelLabel = 'Cita';
    protected static ?string $pluralModelLabel = 'Citas';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Estado de la Cita')
                    ->schema([
                        Forms\Components\TextInput::make('numero_cita')
                            ->label('Número de Cita')
                            ->disabled()
                            ->dehydrated(false),
                        
                        Forms\Components\Select::make('estado')
                            ->label('Estado')
                            ->options(Cita::ESTADOS)
                            ->required()
                            ->native(false),
                        
                        Forms\Components\Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->visibleOn('edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero_cita')
                    ->label('Número')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('nombre_completo')
                    ->label('Ciudadano')
                    ->searchable(['nombres', 'apellidos'])
                    ->sortable()
                    ->weight('medium'),
                
                Tables\Columns\TextColumn::make('documento_completo')
                    ->label('Documento')
                    ->searchable(['numero_documento'])
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('tramite.area.secretaria.nombre')
                    ->label('Secretaría')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->visible(fn () => Auth::user()->canViewAllCitas()),
                
                Tables\Columns\TextColumn::make('tramite.area.nombre')
                    ->label('Área')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->color('primary'),
                
                Tables\Columns\TextColumn::make('tramite.nombre')
                    ->label('Trámite')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('fecha_cita')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color('success')
                    ->weight('medium'),
                
                Tables\Columns\TextColumn::make('hora_cita')
                    ->label('Hora')
                    ->time('H:i')
                    ->sortable()
                    ->color('success')
                    ->weight('medium'),
                
                Tables\Columns\SelectColumn::make('estado')
                    ->label('Estado')
                    ->options(Cita::ESTADOS)
                    ->sortable()
                    ->selectablePlaceholder(false)
                    ->disabled(fn () => !Auth::user()->hasPermission('manage_citas')),
                
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->options(Cita::ESTADOS)
                    ->multiple(),
                
                Tables\Filters\SelectFilter::make('tramite.area.secretaria')
                    ->label('Secretaría')
                    ->relationship('tramite.area.secretaria', 'nombre')
                    ->searchable()
                    ->preload()
                    ->visible(fn () => Auth::user()->canViewAllCitas()),
                
                Tables\Filters\SelectFilter::make('tramite.area')
                    ->label('Área')
                    ->relationship('tramite.area', 'nombre')
                    ->searchable()
                    ->preload()
                    ->visible(fn () => Auth::user()->canViewAllCitas()),
                
                Tables\Filters\SelectFilter::make('tramite')
                    ->label('Trámite')
                    ->relationship('tramite', 'nombre')
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\Filter::make('fecha_cita')
                    ->form([
                        Forms\Components\DatePicker::make('desde')
                            ->label('Desde')
                            ->native(false),
                        Forms\Components\DatePicker::make('hasta')
                            ->label('Hasta')
                            ->native(false),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['desde'], fn ($q) => $q->whereDate('fecha_cita', '>=', $data['desde']))
                            ->when($data['hasta'], fn ($q) => $q->whereDate('fecha_cita', '<=', $data['hasta']));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['desde']) {
                            $indicators['desde'] = 'Desde: ' . \Carbon\Carbon::parse($data['desde'])->format('d/m/Y');
                        }
                        if ($data['hasta']) {
                            $indicators['hasta'] = 'Hasta: ' . \Carbon\Carbon::parse($data['hasta'])->format('d/m/Y');
                        }
                        return $indicators;
                    }),
                
                Tables\Filters\Filter::make('hoy')
                    ->label('Citas de Hoy')
                    ->query(fn ($query) => $query->whereDate('fecha_cita', today()))
                    ->toggle(),
                
                Tables\Filters\Filter::make('esta_semana')
                    ->label('Esta Semana')
                    ->query(fn ($query) => $query->whereBetween('fecha_cita', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ]))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                
                Tables\Actions\EditAction::make()
                    ->visible(fn () => Auth::user()->hasPermission('manage_citas')),
                
                Tables\Actions\Action::make('confirmar')
                    ->label('Confirmar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(fn (Cita $record) => $record->update(['estado' => 'confirmada']))
                    ->visible(fn (Cita $record): bool => 
                        $record->estado === 'programada' && Auth::user()->hasPermission('manage_citas')
                    )
                    ->requiresConfirmation(),
                
                Tables\Actions\Action::make('cancelar')
                    ->label('Cancelar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Cita $record) => $record->update(['estado' => 'cancelada']))
                    ->visible(fn (Cita $record): bool => 
                        in_array($record->estado, ['programada', 'confirmada']) && 
                        Auth::user()->hasPermission('manage_citas')
                    ),
                
                Tables\Actions\Action::make('marcar_atendida')
                    ->label('Marcar Atendida')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->action(fn (Cita $record) => $record->update(['estado' => 'atendida']))
                    ->visible(fn (Cita $record): bool => 
                        in_array($record->estado, ['confirmada', 'en_proceso']) && 
                        Auth::user()->hasPermission('manage_citas')
                    )
                    ->requiresConfirmation(),
                
                Tables\Actions\Action::make('no_asistio')
                    ->label('No Asistió')
                    ->icon('heroicon-o-user-minus')
                    ->color('warning')
                    ->action(fn (Cita $record) => $record->update(['estado' => 'no_asistio']))
                    ->visible(fn (Cita $record): bool => 
                        in_array($record->estado, ['confirmada', 'en_proceso']) && 
                        Auth::user()->hasPermission('manage_citas')
                    )
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('confirmar')
                        ->label('Confirmar Seleccionadas')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['estado' => 'confirmada']))
                        ->visible(fn () => Auth::user()->hasPermission('manage_citas'))
                        ->requiresConfirmation(),
                    
                    Tables\Actions\BulkAction::make('cancelar')
                        ->label('Cancelar Seleccionadas')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['estado' => 'cancelada']))
                        ->visible(fn () => Auth::user()->hasPermission('manage_citas'))
                        ->requiresConfirmation(),
                ])
                ->visible(fn () => Auth::user()->hasPermission('manage_citas')),
            ])
            ->defaultSort('fecha_cita', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Información de la Cita')
                    ->schema([
                        Infolists\Components\TextEntry::make('numero_cita')
                            ->label('Número de Cita')
                            ->copyable()
                            ->weight('bold')
                            ->color('primary'),
                        
                        Infolists\Components\TextEntry::make('estado_label')
                            ->label('Estado')
                            ->badge()
                            ->color(fn (Cita $record): string => match ($record->estado) {
                                'programada' => 'warning',
                                'confirmada' => 'info',
                                'en_proceso' => 'primary',
                                'atendida' => 'success',
                                'cancelada' => 'danger',
                                'no_asistio' => 'gray',
                                default => 'gray',
                            }),
                        
                        Infolists\Components\TextEntry::make('fecha_hora_formateada')
                            ->label('Fecha y Hora')
                            ->icon('heroicon-m-calendar-days')
                            ->color('success'),
                        
                        Infolists\Components\TextEntry::make('tramite.nombre_completo')
                            ->label('Trámite')
                            ->color('primary'),
                    ])
                    ->columns(2),
                
                Infolists\Components\Section::make('Datos del Ciudadano')
                    ->schema([
                        Infolists\Components\TextEntry::make('nombre_completo')
                            ->label('Nombre Completo')
                            ->weight('bold'),
                        
                        Infolists\Components\TextEntry::make('documento_completo')
                            ->label('Documento')
                            ->copyable(),
                        
                        Infolists\Components\TextEntry::make('email')
                            ->label('Correo Electrónico')
                            ->copyable()
                            ->icon('heroicon-m-envelope'),
                        
                        Infolists\Components\TextEntry::make('telefono')
                            ->label('Teléfono')
                            ->copyable()
                            ->icon('heroicon-m-phone'),
                        
                        Infolists\Components\TextEntry::make('direccion')
                            ->label('Dirección')
                            ->visible(fn (Cita $record): bool => !empty($record->direccion))
                            ->icon('heroicon-m-map-pin'),
                    ])
                    ->columns(2),
                
                Infolists\Components\Section::make('Información del Trámite')
                    ->schema([
                        Infolists\Components\TextEntry::make('tramite.area.secretaria.nombre')
                            ->label('Secretaría')
                            ->color('primary'),
                        
                        Infolists\Components\TextEntry::make('tramite.area.nombre')
                            ->label('Área')
                            ->color('success'),
                        
                        Infolists\Components\TextEntry::make('tramite.costo_formateado')
                            ->label('Costo'),
                        
                        Infolists\Components\TextEntry::make('tramite.duracion_minutos')
                            ->label('Duración')
                            ->suffix(' minutos'),
                    ])
                    ->columns(2),
                
                Infolists\Components\Section::make('Información Adicional')
                    ->schema([
                        Infolists\Components\TextEntry::make('observaciones')
                            ->visible(fn (Cita $record): bool => !empty($record->observaciones))
                            ->columnSpanFull(),
                        
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Fecha de Creación')
                            ->dateTime('d/m/Y H:i')
                            ->icon('heroicon-m-clock'),
                        
                        Infolists\Components\TextEntry::make('ip_creacion')
                            ->label('IP de Creación')
                            ->visible(fn () => Auth::user()->hasRole('super_admin')),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCitas::route('/'),
            'view' => Pages\ViewCita::route('/{record}'),
            'edit' => Pages\EditCita::route('/{record}/edit'),
        ];
    }

    /**
     * Filtrar citas según el área del usuario autenticado
     */
    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        
        // Si puede ver todas las citas, no aplicar filtros
        if ($user->canViewAllCitas()) {
            return parent::getEloquentQuery()
                ->with(['tramite.area.secretaria']);
        }
        
        // Si puede ver citas de su área, filtrar por área
        if ($user->canViewAreaCitas()) {
            return parent::getEloquentQuery()
                ->whereHas('tramite', function ($query) use ($user) {
                    $query->where('area_id', $user->area_id);
                })
                ->with(['tramite.area.secretaria']);
        }
        
        // Si no tiene permisos, no mostrar nada
        return parent::getEloquentQuery()->whereRaw('1 = 0');
    }

    /**
     * Determinar si el usuario puede acceder al recurso
     */
    public static function canViewAny(): bool
    {
        $user = Auth::user();
        return $user->hasPermission('view_all_citas') || $user->hasPermission('view_area_citas');
    }

    /**
     * Determinar si el usuario puede crear registros
     */
    public static function canCreate(): bool
    {
        return Auth::user()->hasPermission('manage_citas');
    }
}