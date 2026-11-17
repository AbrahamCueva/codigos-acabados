<?php

namespace App\Filament\Resources\Trabajadors\Schemas;

use App\Enums\TipoTrabajador;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class TrabajadorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Datos del trabajador')
                    ->description('Personal de planta o de servicio que recibe los códigos.')
                    ->icon(Heroicon::OutlinedUser)
                    ->columns(['default' => 1, 'md' => 2])
                    ->components([
                        TextInput::make('nombre')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255)
                            ->autofocus()
                            ->columnSpanFull(),

                        ToggleButtons::make('tipo')
                            ->label('Tipo')
                            ->options(TipoTrabajador::class)
                            ->default(TipoTrabajador::Planta->value)
                            ->inline()
                            ->grouped()
                            ->required(),

                        Toggle::make('activo')
                            ->label('Activo')
                            ->helperText('Los trabajadores inactivos no aparecen al registrar entregas nuevas.')
                            ->default(true)
                            ->onColor('success'),
                    ]),
            ]);
    }
}