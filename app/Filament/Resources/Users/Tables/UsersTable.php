<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn ($record): string => $record->email),

                TextColumn::make('roles.name')
                    ->label('Roles asignados')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'administrador' => 'success',
                        'supervisor' => 'info',
                        'operario' => 'gray',
                        default => 'warning',
                    }),

                TextColumn::make('created_at')
                    ->label('Fecha de registro')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn (): bool =>
                        auth()->user()?->hasRole('administrador') ?? false
                    ),
            ]);
    }
}
