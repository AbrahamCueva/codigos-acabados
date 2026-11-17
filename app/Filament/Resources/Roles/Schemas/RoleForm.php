<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Información del rol')
                    ->description('Define el nombre y los permisos del perfil.')
                    ->columns(1)
                    ->components([
                        TextInput::make('name')
                            ->label('Nombre del rol')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Ejemplo: supervisor, operario o almacenero.'),

                        Section::make('Permisos asignados')
                            ->description('Selecciona los módulos y las acciones permitidas para este rol.')
                            ->schema([
                                Section::make('Códigos')
                                    ->description('Administración de códigos y control de stock.')
                                    ->icon('heroicon-o-qr-code')
                                    ->columns(2)
                                    ->schema([
                                        CheckboxList::make('codigos_permissions')
                                            ->hiddenLabel()
                                            ->options([
                                                'codigos.viewAny' => 'Ver códigos',
                                                'codigos.create' => 'Registrar códigos',
                                                'codigos.update' => 'Editar códigos',
                                                'codigos.update_stock' => 'Modificar stock',
                                                'codigos.delete' => 'Eliminar códigos',
                                            ])
                                            ->bulkToggleable()
                                            ->columns(2)
                                            ->disabled(
                                                fn($record): bool =>
                                                    $record?->name === 'administrador'
                                            ),
                                    ]),

                                Section::make('Entregas')
                                    ->description('Control de entregas y movimientos.')
                                    ->icon('heroicon-o-truck')
                                    ->columns(2)
                                    ->schema([
                                        CheckboxList::make('entregas_permissions')
                                            ->hiddenLabel()
                                            ->options([
                                                'entregas.viewAny' => 'Ver entregas',
                                                'entregas.create' => 'Registrar entregas',
                                                'entregas.update' => 'Editar entregas',
                                                'entregas.delete' => 'Eliminar entregas',
                                            ])
                                            ->bulkToggleable()
                                            ->columns(2)
                                            ->disabled(
                                                fn($record): bool =>
                                                    $record?->name === 'administrador'
                                            ),
                                    ]),

                                Section::make('Trabajadores')
                                    ->description('Administración de trabajadores.')
                                    ->icon('heroicon-o-users')
                                    ->columns(2)
                                    ->schema([
                                        CheckboxList::make('trabajadores_permissions')
                                            ->hiddenLabel()
                                            ->options([
                                                'trabajadores.viewAny' => 'Ver trabajadores',
                                                'trabajadores.create' => 'Registrar trabajadores',
                                                'trabajadores.update' => 'Editar trabajadores',
                                                'trabajadores.delete' => 'Eliminar trabajadores',
                                            ])
                                            ->bulkToggleable()
                                            ->columns(2)
                                            ->disabled(
                                                fn($record): bool =>
                                                    $record?->name === 'administrador'
                                            ),
                                    ]),

                                Section::make('Historial')
                                    ->description('Consulta del registro de actividades del sistema.')
                                    ->icon('heroicon-o-clock')
                                    ->schema([
                                        CheckboxList::make('actividades_permissions')
                                            ->hiddenLabel()
                                            ->options([
                                                'actividades.viewAny' => 'Ver historial de actividades',
                                            ])
                                            ->disabled(
                                                fn($record): bool =>
                                                    $record?->name === 'administrador'
                                            ),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
