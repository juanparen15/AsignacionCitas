<?php
// app/Filament/Resources/CitaResource/Pages/CreateCita.php

namespace App\Filament\Resources\CitaResource\Pages;

use App\Filament\Resources\CitaResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateCita extends CreateRecord
{
    protected static string $resource = CitaResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Generar fecha_hora_cita combinada
        $data['fecha_hora_cita'] = $data['fecha_cita'] . ' ' . $data['hora_cita'];

        // Convertir nombres y apellidos a mayúsculas
        $data['nombres'] = strtoupper($data['nombres']);
        $data['apellidos'] = strtoupper($data['apellidos']);

        // Convertir email a minúsculas
        $data['email'] = strtolower($data['email']);

        // Agregar IP de creación
        $data['ip_creacion'] = request()->ip();

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $record = static::getModel()::create($data);

        // Enviar notificación de éxito
        $this->notify('success', 'Cita creada exitosamente', "Número de cita: {$record->numero_cita}");

        return $record;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Cita creada exitosamente';
    }
}
