<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Datos del usuario')
                    ->description('Información de acceso al sistema.')
                    ->columns(['default' => 1, 'md' => 2])
                    ->components([
                        TextInput::make('name')
                            ->label('Nombre completo')
                            ->required()
                            ->maxLength(255)
                            ->autofocus(),

                        TextInput::make('email')
                            ->label('Correo electrónico')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Select::make('roles')
                            ->label('Roles del usuario')
                            ->relationship(
                                name: 'roles',
                                titleAttribute: 'name'
                            )
                            ->multiple()
                            ->required()
                            ->searchable()
                            ->preload()
                            ->helperText('Puedes asignar uno o varios roles.'),

                        TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->required(fn (string $operation): bool =>
                                $operation === 'create'
                            )
                            ->minLength(8)
                            ->confirmed()
                            ->dehydrated(fn (?string $state): bool =>
                                filled($state)
                            )
                            ->helperText(
                                'Deja este campo vacío si no deseas cambiar la contraseña.'
                            ),

                        TextInput::make('password_confirmation')
                            ->label('Confirmar contraseña')
                            ->password()
                            ->required(fn (string $operation): bool =>
                                $operation === 'create'
                            )
                            ->dehydrated(false),
                    ]),
            ]);
    }
}
