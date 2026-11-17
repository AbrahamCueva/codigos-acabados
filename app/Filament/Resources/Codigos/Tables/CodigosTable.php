<?php

namespace App\Filament\Resources\Codigos\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CodigosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('codigo')
            ->columns([
                TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('stock')
                    ->label('Stock')
                    ->badge()
                    ->sortable()
                    ->alignCenter()
                    ->color(fn(int $state): string => match (true) {
                        $state === 0 => 'danger',
                        $state <= 5 => 'warning',
                        default => 'success',
                    }),

                TextColumn::make('updated_at')
                    ->label('Última actualización')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->visibleFrom('md')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('agotados')
                    ->label('Solo agotados')
                    ->toggle()
                    ->query(fn(Builder $query): Builder => $query->where('stock', 0)),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn(): bool => auth()->user()?->can('codigos.update') ?? false),
            ]);
    }
}