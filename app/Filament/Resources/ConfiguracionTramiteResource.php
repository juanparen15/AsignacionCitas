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
                    ->schema([
                        Forms\Components\TimePicker::make('hora_inicio')
                            ->label('Hora de Inicio')
                            ->required()
                            ->default('08:00')
                            ->seconds(false),
                        
                        Forms\Components\TimePicker::make('hora_fin')
                            ->label('Hora de Fin')
                            ->required()
                            ->default('17:00')
                            ->seconds(false)
                            ->after('hora_inicio'),
                        
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
                            ->columns(4),
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
                            ->label('')
                            ->content('Las citas se podrán agendar desde mañana hasta 30 días en el futuro')
                            ->helperText('Basado en la configuración actual'),
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
                
                Tables\Columns\TextColumn::make('hora_inicio')
                    ->label('Horario')
                    ->formatStateUsing(fn (ConfiguracionTramite $record): string => 
                        $record->hora_inicio->format('H:i') . ' - ' . $record->hora_fin->format('H:i')
                    ),
                
                Tables\Columns\TextColumn::make('citas_por_hora')
                    ->label('Citas/Hora')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('dias_anticipacion_minima')
                    ->label('Días Min.')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('dias_anticipacion_maxima')
                    ->label('Días Max.')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('requiere_documentos')
                    ->label('Docs')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tramite.area.secretaria')
                    ->label('Secretaría')
                    ->relationship('tramite.area.secretaria', 'nombre')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListConfiguracionTramites::route('/'),
            'create' => Pages\CreateConfiguracionTramite::route('/create'),
            'edit' => Pages\EditConfiguracionTramite::route('/{record}/edit'),
        ];
    }
}