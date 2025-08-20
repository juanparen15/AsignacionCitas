<?php

namespace App\Filament\Resources\ConfiguracionTramiteResource\Pages;

use App\Filament\Resources\ConfiguracionTramiteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConfiguracionTramites extends ListRecords
{
    protected static string $resource = ConfiguracionTramiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
