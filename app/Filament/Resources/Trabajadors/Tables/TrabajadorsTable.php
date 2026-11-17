<?php

namespace App\Filament\Resources\Trabajadors\Tables;

use App\Enums\TipoTrabajador;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class TrabajadorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('nombre')
            ->columns([
                TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->sortable(),

                IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean()
                    ->alignCenter(),

                TextColumn::make('entregas_count')
                    ->label('Entregas')
                    ->counts('entregas')
                    ->badge()
                    ->color('gray')
                    ->alignCenter()
                    ->visibleFrom('md'),
            ])
            ->filters([
                SelectFilter::make('tipo')
                    ->label('Tipo')
                    ->options(TipoTrabajador::class),

                TernaryFilter::make('activo')
                    ->label('Estado')
                    ->placeholder('Todos')
                    ->trueLabel('Solo activos')
                    ->falseLabel('Solo inactivos'),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn (): bool => auth()->user()?->can('trabajadores.update') ?? false),
            ]);
    }
}
