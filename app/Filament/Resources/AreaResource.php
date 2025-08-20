<?php
// app/Filament/Resources/AreaResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\AreaResource\Pages;
use App\Models\Area;
use App\Models\Secretaria;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class AreaResource extends Resource
{
    protected static ?string $model = Area::class;
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $navigationLabel = 'Áreas';
    protected static ?string $modelLabel = 'Área';
    protected static ?string $pluralModelLabel = 'Áreas';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('secretaria_id')
                    ->label('Secretaría')
                    ->options(Secretaria::activas()->pluck('nombre', 'id'))
                    ->required()
                    ->searchable()
                    ->preload(),
                
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                
                Forms\Components\Textarea::make('descripcion')
                    ->rows(3)
                    ->columnSpanFull(),
                
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Toggle::make('activa')
                            ->default(true)
                            ->helperText('Determina si el área está disponible para agendar citas'),
                        
                        Forms\Components\TextInput::make('orden')
                            ->numeric()
                            ->default(0)
                            ->helperText('Orden de aparición (menor número aparece primero)'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('secretaria.nombre')
                    ->label('Secretaría')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('tramites_count')
                    ->counts('tramites')
                    ->label('Trámites')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('activa')
                    ->boolean()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('orden')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('secretaria_id')
                    ->label('Secretaría')
                    ->options(Secretaria::activas()->pluck('nombre', 'id'))
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\TernaryFilter::make('activa')
                    ->label('Estado')
                    ->boolean()
                    ->trueLabel('Solo activas')
                    ->falseLabel('Solo inactivas')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->visible(fn (): bool => Auth::user()->hasPermission('manage_areas') || 
                                            Auth::user()->hasRole('super_admin')),
                
                Tables\Actions\EditAction::make()
                    ->visible(fn (): bool => Auth::user()->hasPermission('manage_areas') || 
                                            Auth::user()->hasRole('super_admin')),
                
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (): bool => Auth::user()->hasPermission('manage_areas') || 
                                            Auth::user()->hasRole('super_admin'))
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('orden');
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->hasPermission('manage_areas') || 
               Auth::user()->hasRole('super_admin');
    }

    public static function canCreate(): bool
    {
        return Auth::user()->hasPermission('manage_areas') || 
               Auth::user()->hasRole('super_admin');
    }

    public static function canEdit(Model $record): bool
    {
        return Auth::user()->hasPermission('manage_areas') || 
               Auth::user()->hasRole('super_admin');
    }

    public static function canDelete(Model $record): bool
    {
        return Auth::user()->hasPermission('manage_areas') || 
               Auth::user()->hasRole('super_admin');
    }

    public static function canDeleteAny(): bool
    {
        return Auth::user()->hasPermission('manage_areas') || 
               Auth::user()->hasRole('super_admin');
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAreas::route('/'),
            'create' => Pages\CreateArea::route('/create'),
            'edit' => Pages\EditArea::route('/{record}/edit'),
        ];
    }
}