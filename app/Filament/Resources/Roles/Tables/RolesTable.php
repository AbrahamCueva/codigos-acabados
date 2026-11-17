<?php

namespace App\Filament\Resources\Roles\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RolesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Rol')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'administrador' => 'success',
                        'supervisor' => 'info',
                        'operario' => 'gray',
                        default => 'warning',
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('permissions_count')
                    ->label('Cantidad de permisos')
                    ->counts('permissions')
                    ->badge()
                    ->color('primary')
                    ->alignCenter(),

                TextColumn::make('users_count')
                    ->label('Usuarios asignados')
                    ->counts('users')
                    ->badge()
                    ->color('info')
                    ->alignCenter(),

                TextColumn::make('created_at')
                    ->label('Fecha de creación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(
                        fn(): bool =>
                            auth()->user()?->hasRole('administrador') ?? false
                    ),

                DeleteAction::make()
                    ->visible(
                        fn(): bool =>
                            auth()->user()?->hasRole('administrador') ?? false
                    )
                    ->disabled(
                        fn($record): bool =>
                            $record->name === 'administrador'
                            || $record->users()->exists()
                    )
                    ->tooltip(fn($record): ?string => match (true) {
                        $record->name === 'administrador' =>
                            'El rol administrador está protegido.',
                        $record->users()->exists() =>
                            'No puedes eliminar un rol que tiene usuarios asignados.',
                        default => null,
                    })
                    ->before(function ($record): void {
                        activity('roles')
                            ->causedBy(auth()->user())
                            ->performedOn($record)
                            ->withProperties([
                                'snapshot' => [
                                    'name' => $record->name,
                                    'permissions' => $record->permissions()
                                        ->pluck('name')
                                        ->all(),
                                ],
                            ])
                            ->log('Rol eliminado');
                    }),
            ]);
    }
}
