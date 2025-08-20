<?php

namespace App\Filament\Resources\AppointmentResource\Widgets;

use Filament\Widgets\ChartWidget;

class AppointmentCalendarWidget extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected function getData(): array
    {
        return [
            //
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
