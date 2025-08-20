<?php

// app/Filament/Resources/AppointmentResource.php
namespace App\Filament\Resources;

use App\Filament\Resources\AppointmentResource\Pages;
use App\Models\Appointment;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Citas';
    protected static ?string $pluralLabel = 'Citas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información de la Cita')
                    ->schema([
                        Forms\Components\Select::make('service_id')
                            ->label('Servicio')
                            ->options(Service::where('is_active', true)->pluck('name', 'id'))
                            ->required()
                            ->reactive(),

                        Forms\Components\DateTimePicker::make('appointment_date')
                            ->label('Fecha y Hora')
                            ->required()
                            ->minDate(now())
                            ->native(false),

                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'pending' => 'Pendiente',
                                'confirmed' => 'Confirmada',
                                'cancelled' => 'Cancelada',
                                'completed' => 'Completada',
                            ])
                            ->default('pending')
                            ->required(),
                    ])->columns(2),

                Section::make('Información del Cliente')
                    ->schema([
                        Forms\Components\TextInput::make('client_name')
                            ->label('Nombre del Cliente')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('client_email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('client_phone')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('notes')
                            ->label('Notas del Cliente')
                            ->rows(3),
                    ])->columns(2),

                Section::make('Información Administrativa')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('Precio')
                            ->numeric()
                            ->prefix('$'),

                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Notas Administrativas')
                            ->rows(3),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('appointment_date')
                    ->label('Fecha y Hora')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('service.name')
                    ->label('Servicio')
                    ->searchable(),

                Tables\Columns\TextColumn::make('client_name')
                    ->label('Cliente')
                    ->searchable(),

                Tables\Columns\TextColumn::make('client_email')
                    ->label('Email')
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirmed',
                        'danger' => 'cancelled',
                        'primary' => 'completed',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => 'Pendiente',
                        'confirmed' => 'Confirmada',
                        'cancelled' => 'Cancelada',
                        'completed' => 'Completada',
                    }),

                Tables\Columns\TextColumn::make('price')
                    ->label('Precio')
                    ->money('COP'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'confirmed' => 'Confirmada',
                        'cancelled' => 'Cancelada',
                        'completed' => 'Completada',
                    ]),

                Tables\Filters\SelectFilter::make('service')
                    ->label('Servicio')
                    ->relationship('service', 'name'),

                Tables\Filters\Filter::make('today')
                    ->label('Hoy')
                    ->query(fn(Builder $query): Builder => $query->whereDate('appointment_date', today())),

                Tables\Filters\Filter::make('upcoming')
                    ->label('Próximas')
                    ->query(fn(Builder $query): Builder => $query->where('appointment_date', '>', now())),
            ])
            ->actions([
                Tables\Actions\Action::make('confirm')
                    ->label('Confirmar')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(fn(Appointment $record) => $record->confirm())
                    ->visible(fn(Appointment $record): bool => $record->status === 'pending'),

                Tables\Actions\Action::make('cancel')
                    ->label('Cancelar')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->action(fn(Appointment $record) => $record->cancel())
                    ->visible(fn(Appointment $record): bool => $record->canBeCancelled()),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('appointment_date', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }
}
