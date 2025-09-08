<?php
// app/Filament/Resources/ConfiguracionTramiteResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\ConfiguracionTramiteResource\Pages;
use App\Models\ConfiguracionTramite;
use App\Models\Tramite;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class ConfiguracionTramiteResource extends Resource
{
    protected static ?string $model = ConfiguracionTramite::class;
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Configuración Trámites';
    protected static ?string $modelLabel = 'Configuración';
    protected static ?string $pluralModelLabel = 'Configuraciones';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Trámite')
                    ->schema([
                        Forms\Components\Select::make('tramite_id')
                            ->label('Trámite')
                            ->options(Tramite::with(['area.secretaria'])
                                ->get()
                                ->mapWithKeys(fn ($tramite) => [
                                    $tramite->id => $tramite->area->secretaria->nombre 
                                        . ' - ' . $tramite->area->nombre 
                                        . ' - ' . $tramite->nombre
                                ]))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->disabled(fn (?ConfiguracionTramite $record) => $record !== null),
                    ]),
                
                Forms\Components\Section::make('Horarios de Atención')
                    ->description('Configure los horarios disponibles para este trámite')
                    ->schema([
                        // Información sobre horario de almuerzo
                        Forms\Components\Placeholder::make('horario_almuerzo_info')
                            ->label('Información Importante')
                            ->content(function () {
                                $horarioAlmuerzo = ConfiguracionTramite::getHorarioAlmuerzo();
                                return "⚠️ Horario de almuerzo automáticamente excluido: {$horarioAlmuerzo['inicio']} - {$horarioAlmuerzo['fin']}";
                            })
                            ->columnSpanFull(),

                        Forms\Components\TimePicker::make('hora_inicio')
                            ->label('Hora de Inicio')
                            ->required()
                            ->default('08:00')
                            ->seconds(false)
                            ->helperText('Hora de inicio del horario de atención'),
                        
                        Forms\Components\TimePicker::make('hora_fin')
                            ->label('Hora de Fin')
                            ->required()
                            ->default('17:00')
                            ->seconds(false)
                            ->after('hora_inicio')
                            ->helperText('Hora de fin del horario de atención'),
                        
                        Forms\Components\CheckboxList::make('dias_disponibles')
                            ->label('Días Disponibles')
                            ->options([
                                '1' => 'Lunes',
                                '2' => 'Martes',
                                '3' => 'Miércoles',
                                '4' => 'Jueves',
                                '5' => 'Viernes',
                                '6' => 'Sábado',
                                '7' => 'Domingo',
                            ])
                            ->default(['1', '2', '3', '4', '5'])
                            ->required()
                            ->columnSpanFull()
                            ->columns(4)
                            ->helperText('Seleccione los días de la semana disponibles para citas'),

                        // Horarios efectivos (calculados)
                        Forms\Components\Placeholder::make('horarios_efectivos')
                            ->label('Horarios Efectivos')
                            ->content(function (Forms\Get $get) {
                                $horaInicio = $get('hora_inicio') ?? '08:00';
                                $horaFin = $get('hora_fin') ?? '17:00';
                                $horarioAlmuerzo = ConfiguracionTramite::getHorarioAlmuerzo();
                                
                                return "🕐 Mañana: {$horaInicio} - {$horarioAlmuerzo['inicio']} | 🕐 Tarde: {$horarioAlmuerzo['fin']} - {$horaFin}";
                            })
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Capacidad y Restricciones')
                    ->schema([
                        Forms\Components\TextInput::make('citas_por_hora')
                            ->label('Citas por Hora')
                            ->numeric()
                            ->required()
                            ->default(4)
                            ->minValue(1)
                            ->maxValue(20)
                            ->helperText('Número máximo de citas que se pueden agendar por hora'),
                        
                        Forms\Components\TextInput::make('dias_anticipacion_minima')
                            ->label('Días de Anticipación Mínima')
                            ->numeric()
                            ->required()
                            ->default(1)
                            ->minValue(0)
                            ->helperText('Días mínimos de anticipación para agendar una cita'),
                        
                        Forms\Components\TextInput::make('dias_anticipacion_maxima')
                            ->label('Días de Anticipación Máxima')
                            ->numeric()
                            ->required()
                            ->default(30)
                            ->minValue(1)
                            ->helperText('Días máximos de anticipación para agendar una cita'),
                        
                        Forms\Components\Placeholder::make('info')
                            ->label('Información Calculada')
                            ->content(function (Forms\Get $get) {
                                $minDias = $get('dias_anticipacion_minima') ?? 1;
                                $maxDias = $get('dias_anticipacion_maxima') ?? 30;
                                $fechaMin = now()->addDays($minDias)->format('d/m/Y');
                                $fechaMax = now()->addDays($maxDias)->format('d/m/Y');
                                return "Las citas se podrán agendar desde el {$fechaMin} hasta el {$fechaMax}";
                            }),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Días Inhabiles')
                    ->schema([
                        Forms\Components\Repeater::make('dias_inhabiles')
                            ->label('Fechas Inhabiles')
                            ->schema([
                                Forms\Components\DatePicker::make('fecha')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d/m/Y'),
                                
                                Forms\Components\TextInput::make('motivo')
                                    ->placeholder('Ej: Día festivo, Mantenimiento, etc.')
                                    ->maxLength(255),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('Agregar Fecha Inhábil')
                            ->helperText('Fechas específicas donde no se pueden agendar citas')
                            ->columnSpanFull(),
                    ]),
                
                Forms\Components\Section::make('Documentos Requeridos')
                    ->schema([
                        Forms\Components\Toggle::make('requiere_documentos')
                            ->label('Requiere Documentos')
                            ->live()
                            ->columnSpanFull(),
                        
                        Forms\Components\Repeater::make('documentos_requeridos')
                            ->label('Documentos')
                            ->schema([
                                Forms\Components\TextInput::make('nombre')
                                    ->label('Nombre del Documento')
                                    ->required()
                                    ->placeholder('Ej: Cédula de Ciudadanía, Certificado laboral, etc.'),
                                
                                Forms\Components\Toggle::make('obligatorio')
                                    ->label('Obligatorio')
                                    ->default(true),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('Agregar Documento')
                            ->visible(fn (Forms\Get $get): bool => $get('requiere_documentos'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tramite.area.secretaria.nombre')
                    ->label('Secretaría')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('tramite.area.nombre')
                    ->label('Área')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('tramite.nombre')
                    ->label('Trámite')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                
                Tables\Columns\TextColumn::make('horario_completo')
                    ->label('Horario (Sin Almuerzo)')
                    ->formatStateUsing(function (ConfiguracionTramite $record): string {
                        $horarioAlmuerzo = ConfiguracionTramite::getHorarioAlmuerzo();
                        $inicio = $record->hora_inicio->format('H:i');
                        $fin = $record->hora_fin->format('H:i');
                        return "{$inicio}-{$horarioAlmuerzo['inicio']} | {$horarioAlmuerzo['fin']}-{$fin}";
                    })
                    ->tooltip('Horarios efectivos excluyendo almuerzo'),
                
                Tables\Columns\TextColumn::make('citas_por_hora')
                    ->label('Citas/Hora')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('anticipacion')
                    ->label('Anticipación')
                    ->formatStateUsing(fn (ConfiguracionTramite $record): string => 
                        $record->dias_anticipacion_minima . '-' . $record->dias_anticipacion_maxima . ' días'
                    ),
                
                Tables\Columns\IconColumn::make('requiere_documentos')
                    ->label('Docs')
                    ->boolean(),

                Tables\Columns\TextColumn::make('horas_disponibles_count')
                    ->label('Horas/Día')
                    ->formatStateUsing(function (ConfiguracionTramite $record): string {
                        $horas = $record->getHorasDisponibles(now());
                        return count($horas) . ' horas';
                    })
                    ->tooltip('Total de horas disponibles por día (excluyendo almuerzo)'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tramite.area.secretaria')
                    ->label('Secretaría')
                    ->relationship('tramite.area.secretaria', 'nombre')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('horario_amplio')
                    ->label('Horario Amplio')
                    ->query(function ($query) {
                        return $query->whereRaw('TIME(hora_fin) - TIME(hora_inicio) >= "08:00:00"');
                    })
                    ->toggle(),

                Tables\Filters\Filter::make('muchas_citas_por_hora')
                    ->label('Alta Capacidad')
                    ->query(fn ($query) => $query->where('citas_por_hora', '>=', 6))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\Action::make('ver_horarios')
                    ->label('Ver Horarios')
                    ->icon('heroicon-m-clock')
                    ->color('info')
                    ->action(function (ConfiguracionTramite $record) {
                        $horas = $record->getHorasDisponibles(now());
                        $horarioAlmuerzo = ConfiguracionTramite::getHorarioAlmuerzo();
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Horarios Disponibles')
                            ->body(
                                "Trámite: {$record->tramite->nombre}\n" .
                                "Horarios: " . implode(', ', $horas) . "\n" .
                                "Almuerzo excluido: {$horarioAlmuerzo['inicio']} - {$horarioAlmuerzo['fin']}"
                            )
                            ->info()
                            ->send();
                    }),

                Tables\Actions\ViewAction::make()
                    ->visible(fn (): bool => Auth::user()->hasPermission('manage_configuracion') || 
                                            Auth::user()->hasRole('super_admin')),
                
                Tables\Actions\EditAction::make()
                    ->visible(fn (): bool => Auth::user()->hasPermission('manage_configuracion') || 
                                            Auth::user()->hasRole('super_admin')),
                
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (): bool => Auth::user()->hasPermission('manage_configuracion') || 
                                            Auth::user()->hasRole('super_admin'))
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('info_almuerzo')
                    ->label('Info Horario Almuerzo')
                    ->icon('heroicon-m-information-circle')
                    ->color('warning')
                    ->action(function () {
                        $horarioAlmuerzo = ConfiguracionTramite::getHorarioAlmuerzo();
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Información del Horario de Almuerzo')
                            ->body(
                                "🍽️ Horario: {$horarioAlmuerzo['inicio']} - {$horarioAlmuerzo['fin']}\n" .
                                "📝 {$horarioAlmuerzo['mensaje']}\n\n" .
                                "Este horario se excluye automáticamente de todas las configuraciones."
                            )
                            ->warning()
                            ->persistent()
                            ->send();
                    }),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Información del Trámite')
                    ->schema([
                        Infolists\Components\TextEntry::make('tramite.nombre_completo')
                            ->label('Trámite Completo'),
                        
                        Infolists\Components\TextEntry::make('tramite.costo_formateado')
                            ->label('Costo'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Configuración de Horarios')
                    ->schema([
                        Infolists\Components\TextEntry::make('horario_atencion')
                            ->label('Horario de Atención')
                            ->formatStateUsing(function (ConfiguracionTramite $record): string {
                                return $record->hora_inicio->format('H:i') . ' - ' . $record->hora_fin->format('H:i');
                            }),

                        Infolists\Components\TextEntry::make('horario_almuerzo')
                            ->label('Horario de Almuerzo (Excluido)')
                            ->formatStateUsing(function (): string {
                                $horarioAlmuerzo = ConfiguracionTramite::getHorarioAlmuerzo();
                                return $horarioAlmuerzo['inicio'] . ' - ' . $horarioAlmuerzo['fin'];
                            })
                            ->color('warning'),

                        Infolists\Components\TextEntry::make('horarios_efectivos')
                            ->label('Horarios Efectivos')
                            ->formatStateUsing(function (ConfiguracionTramite $record): string {
                                $horarioAlmuerzo = ConfiguracionTramite::getHorarioAlmuerzo();
                                $inicio = $record->hora_inicio->format('H:i');
                                $fin = $record->hora_fin->format('H:i');
                                return "Mañana: {$inicio} - {$horarioAlmuerzo['inicio']} | Tarde: {$horarioAlmuerzo['fin']} - {$fin}";
                            })
                            ->color('success'),

                        Infolists\Components\TextEntry::make('dias_disponibles')
                            ->label('Días Disponibles')
                            ->formatStateUsing(function (ConfiguracionTramite $record): string {
                                $dias = [
                                    '1' => 'Lunes', '2' => 'Martes', '3' => 'Miércoles',
                                    '4' => 'Jueves', '5' => 'Viernes', '6' => 'Sábado', '7' => 'Domingo'
                                ];
                                return collect($record->dias_disponibles)
                                    ->map(fn($dia) => $dias[$dia] ?? $dia)
                                    ->join(', ');
                            }),

                        Infolists\Components\TextEntry::make('citas_por_hora')
                            ->label('Capacidad por Hora'),

                        Infolists\Components\TextEntry::make('total_horas_dia')
                            ->label('Total Horas por Día')
                            ->formatStateUsing(function (ConfiguracionTramite $record): string {
                                $horas = $record->getHorasDisponibles(now());
                                return count($horas) . ' horas disponibles';
                            }),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Restricciones')
                    ->schema([
                        Infolists\Components\TextEntry::make('dias_anticipacion_minima')
                            ->label('Días Mínimos de Anticipación'),

                        Infolists\Components\TextEntry::make('dias_anticipacion_maxima')
                            ->label('Días Máximos de Anticipación'),

                        Infolists\Components\TextEntry::make('periodo_disponible')
                            ->label('Período Disponible')
                            ->formatStateUsing(function (ConfiguracionTramite $record): string {
                                $fechaMin = $record->fecha_minima_cita->format('d/m/Y');
                                $fechaMax = $record->fecha_maxima_cita->format('d/m/Y');
                                return "Desde {$fechaMin} hasta {$fechaMax}";
                            }),
                    ])
                    ->columns(2),
            ]);
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->hasPermission('manage_configuracion') || 
               Auth::user()->hasRole('super_admin');
    }

    public static function canCreate(): bool
    {
        return Auth::user()->hasPermission('manage_configuracion') || 
               Auth::user()->hasRole('super_admin');
    }

    public static function canEdit(Model $record): bool
    {
        return Auth::user()->hasPermission('manage_configuracion') || 
               Auth::user()->hasRole('super_admin');
    }

    public static function canDelete(Model $record): bool
    {
        return Auth::user()->hasPermission('manage_configuracion') || 
               Auth::user()->hasRole('super_admin');
    }

    public static function canDeleteAny(): bool
    {
        return Auth::user()->hasPermission('manage_configuracion') || 
               Auth::user()->hasRole('super_admin');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConfiguracionTramites::route('/'),
            'create' => Pages\CreateConfiguracionTramite::route('/create'),
            'view' => Pages\ViewConfiguracionTramite::route('/{record}'),
            'edit' => Pages\EditConfiguracionTramite::route('/{record}/edit'),
        ];
    }
}