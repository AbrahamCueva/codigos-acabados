<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TipoTrabajador: string implements HasColor, HasLabel
{
    case Planta = 'planta';
    case Servicio = 'servicio';

    public function getLabel(): string
    {
        return match ($this) {
            self::Planta => 'Planta',
            self::Servicio => 'Servicio',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Planta => 'info',
            self::Servicio => 'warning',
        };
    }
}