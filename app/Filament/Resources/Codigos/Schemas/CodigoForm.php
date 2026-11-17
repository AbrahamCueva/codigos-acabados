<?php

namespace App\Filament\Resources\Codigos\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class CodigoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Datos del código')
                    ->description('Cuando llegan códigos nuevos, actualiza el stock desde aquí.')
                    ->icon(Heroicon::OutlinedQrCode)
                    ->columns(['default' => 1, 'md' => 2])
                    ->components([
                        TextInput::make('codigo')
                            ->label('Código')
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->default(0)
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->autofocus()
                            ->disabled(fn (string $operation): bool =>
                                ! auth()->user()?->can(
                                    $operation === 'create'
                                        ? 'codigos.create'
                                        : 'codigos.update'
                                )
                            )
                            ->dehydrated(fn (string $operation): bool =>
                                auth()->user()?->can(
                                    $operation === 'create'
                                        ? 'codigos.create'
                                        : 'codigos.update'
                                ) ?? false
                            ),

                        TextInput::make('stock')
                            ->label('Stock')
                            ->helperText('Se queda en 0 cuando se acaba, hasta que lleguen nuevos.')
                            ->required()
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->default(0)
                            ->disabled(fn (string $operation): bool =>
                                ! auth()->user()?->can(
                                    $operation === 'create'
                                        ? 'codigos.create'
                                        : 'codigos.update_stock'
                                )
                            )
                            ->dehydrated(fn (string $operation): bool =>
                                auth()->user()?->can(
                                    $operation === 'create'
                                        ? 'codigos.create'
                                        : 'codigos.update_stock'
                                ) ?? false
                            ),
                    ]),
            ]);
    }
}
