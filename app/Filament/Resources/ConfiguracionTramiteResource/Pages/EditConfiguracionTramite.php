<?php

namespace App\Filament\Resources\ConfiguracionTramiteResource\Pages;

use App\Filament\Resources\ConfiguracionTramiteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConfiguracionTramite extends EditRecord
{
    protected static string $resource = ConfiguracionTramiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
