<?php
// app/Filament/Resources/TramiteResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\TramiteResource\Pages;
use App\Models\Tramite;
use App\Models\Secretaria;
use App\Models\Area;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Builder;

class TramiteResource extends Resource
{
    protected static ?string $model = Tramite::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Trámites';
    protected static ?string $modelLabel = 'Trámite';
    protected static ?string $pluralModelLabel = 'Trámites';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        Forms\Components\Select::make('secretaria_id')
                            ->label('Secretaría')
                            ->options(Secretaria::activas()->pluck('nombre', 'id'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn (Forms\Set $set) => $set('area_id', null)),
                        
                        Forms\Components\Select::make('area_id')
                            ->label('Área')
                            ->options(fn (Get $get): array => 
                                Area::where('secretaria_id', $get('secretaria_id'))
                                    ->where('activa', true)
                                    ->pluck('nombre', 'id')
                                    ->toArray()
                            )
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live(),
                        
                        Forms\Components\TextInput::make('nombre')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        
                        Forms\Components\Textarea::make('descripcion')
                            ->rows(3)
                            ->columnSpanFull(),
                        
                        Forms\Components\Textarea::make('requisitos')
                            ->label('Requisitos')
                            ->rows(4)
                            ->columnSpanFull()
                            ->helperText('Documenta los requisitos necesarios para este trámite'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Configuración de Costos y Tiempo')
                    ->schema([
                        Forms\Components\Toggle::make('es_gratuito')
                            ->label('Trámite Gratuito')
                            ->default(true)
                            ->live()
                            ->columnSpanFull(),
                        
                        Forms\Components\TextInput::make('costo')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->hidden(fn (Get $get): bool => $get('es_gratuito'))
                            ->required(fn (Get $get): bool => !$get('es_gratuito')),
                        
                        Forms\Components\TextInput::make('duracion_minutos')
                            ->label('Duración (minutos)')
                            ->numeric()
                            ->default(30)
                            ->required()
                            ->helperText('Tiempo estimado para atender este trámite'),
                        
                        Forms\Components\Toggle::make('activo')
                            ->default(true)
                            ->helperText('Determina si el trámite está disponible para agendar citas'),
                        
                        Forms\Components\TextInput::make('orden')
                            ->numeric()
                            ->default(0)
                            ->helperText('Orden de aparición (menor número aparece primero)'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('area.secretaria.nombre')
                    ->label('Secretaría')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('area.nombre')
                    ->label('Área')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                
                Tables\Columns\TextColumn::make('costo_formateado')
                    ->label('Costo'),
                
                Tables\Columns\TextColumn::make('duracion_minutos')
                    ->label('Duración')
                    ->suffix(' min')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('citas_activas_count')
                    ->counts('citasActivas')
                    ->label('Citas Programadas')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('activo')
                    ->boolean()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('orden')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('secretaria')
                    ->label('Secretaría')
                    ->relationship('area.secretaria', 'nombre')
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\SelectFilter::make('area')
                    ->label('Área')
                    ->relationship('area', 'nombre')
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\TernaryFilter::make('es_gratuito')
                    ->label('Tipo de Costo')
                    ->boolean()
                    ->trueLabel('Solo gratuitos')
                    ->falseLabel('Solo con costo')
                    ->native(false),
                
                Tables\Filters\TernaryFilter::make('activo')
                    ->label('Estado')
                    ->boolean()
                    ->trueLabel('Solo activos')
                    ->falseLabel('Solo inactivos')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('configurar')
                    ->label('Configurar')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->url(fn (Tramite $record): string => 
                        route('filament.admin.resources.configuracion-tramites.edit', $record->configuracion)
                    )
                    ->visible(fn (Tramite $record): bool => $record->configuracion !== null),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('orden');
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
            'index' => Pages\ListTramites::route('/'),
            'create' => Pages\CreateTramite::route('/create'),
            'edit' => Pages\EditTramite::route('/{record}/edit'),
        ];
    }
}