<?php
// app/Filament/Resources/UserResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Models\Role;
use App\Models\Area;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Usuarios';
    protected static ?string $modelLabel = 'Usuario';
    protected static ?string $pluralModelLabel = 'Usuarios';
    protected static ?string $navigationGroup = 'Administración';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Personal')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre Completo')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        
                        Forms\Components\TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('phone')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('300 123 4567'),
                        
                        Forms\Components\TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->minLength(8)
                            ->maxLength(255)
                            ->helperText('Mínimo 8 caracteres. Dejar vacío para mantener la actual.')
                            ->columnSpan(2),
                        
                        Forms\Components\Toggle::make('active')
                            ->label('Usuario Activo')
                            ->default(true)
                            ->helperText('Solo los usuarios activos pueden acceder al sistema'),
                        
                        Forms\Components\DateTimePicker::make('email_verified_at')
                            ->label('Email Verificado')
                            ->helperText('Fecha y hora de verificación del email')
                            ->displayFormat('d/m/Y H:i')
                            ->native(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Asignaciones')
                    ->schema([
                        Forms\Components\Select::make('area_id')
                            ->label('Área Asignada')
                            ->options(Area::active()->with('secretaria')->get()->mapWithKeys(function ($area) {
                                return [$area->id => $area->secretaria->nombre . ' - ' . $area->nombre];
                            }))
                            ->searchable()
                            ->preload()
                            ->helperText('Área a la que pertenece el usuario (opcional)')
                            ->columnSpanFull(),
                        
                        Forms\Components\CheckboxList::make('roles')
                            ->label('Roles Asignados')
                            ->relationship('roles', 'display_name')
                            ->options(Role::active()->pluck('display_name', 'id'))
                            ->descriptions(Role::active()->pluck('description', 'id')->filter()->toArray())
                            ->columns(2)
                            ->columnSpanFull()
                            ->helperText('Seleccione uno o más roles para el usuario'),
                    ]),

                Forms\Components\Section::make('Notas Adicionales')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Notas')
                            ->rows(4)
                            ->columnSpanFull()
                            ->helperText('Información adicional sobre el usuario'),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                
                Tables\Columns\TextColumn::make('email')
                    ->label('Correo')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->icon('heroicon-m-envelope'),
                
                Tables\Columns\TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable()
                    ->toggleable()
                    ->icon('heroicon-m-phone'),
                
                Tables\Columns\TextColumn::make('area.secretaria.nombre')
                    ->label('Secretaría')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->color('primary')
                    ->weight('medium'),
                
                Tables\Columns\TextColumn::make('area.nombre')
                    ->label('Área')
                    ->searchable()
                    ->sortable()
                    ->color('success'),
                
                Tables\Columns\TextColumn::make('display_roles')
                    ->label('Roles')
                    ->badge()
                    ->separator(',')
                    ->color('warning'),
                
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('Verificado')
                    ->boolean()
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\IconColumn::make('active')
                    ->label('Activo')
                    ->boolean()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('active')
                    ->label('Estado')
                    ->boolean()
                    ->trueLabel('Solo activos')
                    ->falseLabel('Solo inactivos')
                    ->native(false),
                
                Tables\Filters\TernaryFilter::make('email_verified_at')
                    ->label('Email Verificado')
                    ->boolean()
                    ->trueLabel('Solo verificados')
                    ->falseLabel('Solo no verificados')
                    ->native(false),
                
                Tables\Filters\SelectFilter::make('area_id')
                    ->label('Área')
                    ->options(Area::active()->with('secretaria')->get()->mapWithKeys(function ($area) {
                        return [$area->id => $area->secretaria->nombre . ' - ' . $area->nombre];
                    }))
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\SelectFilter::make('roles')
                    ->label('Rol')
                    ->relationship('roles', 'display_name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make('verify_email')
                    ->label('Verificar Email')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->action(function (User $record) {
                        $record->markEmailAsVerified();
                    })
                    ->visible(fn (User $record): bool => !$record->hasVerifiedEmail())
                    ->requiresConfirmation(),
                
                Tables\Actions\Action::make('toggle_status')
                    ->label(fn (User $record): string => $record->active ? 'Desactivar' : 'Activar')
                    ->icon(fn (User $record): string => $record->active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (User $record): string => $record->active ? 'danger' : 'success')
                    ->action(function (User $record) {
                        $record->update(['active' => !$record->active]);
                    })
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                    
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activar Seleccionados')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each->update(['active' => true]);
                        })
                        ->requiresConfirmation(),
                    
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Desactivar Seleccionados')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function ($records) {
                            $records->each->update(['active' => false]);
                        })
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['area.secretaria', 'roles']);
    }
}
