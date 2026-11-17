<?php

namespace App\Filament\Resources\Entregas\Tables;

use App\Enums\TipoTrabajador;
use App\Models\Entrega;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EntregasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('fecha', 'desc')
            ->columns([
                TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('codigo_snapshot')
                    ->label('Código')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->placeholder('Sin registro histórico'),

                TextColumn::make('trabajador_nombre_snapshot')
                    ->label('Trabajador')
                    ->searchable()
                    ->description(
                        fn(Entrega $record): string =>
                            $record->trabajador?->tipo?->getLabel() ?? 'Sin tipo'
                    )
                    ->placeholder('Sin registro histórico'),

                TextColumn::make('rollos_entregados')
                    ->label('Entregados')
                    ->numeric()
                    ->alignCenter(),

                TextColumn::make('rollos_restantes')
                    ->label('Restantes')
                    ->numeric()
                    ->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('trabajador')
                    ->label('Trabajador')
                    ->relationship('trabajador', 'nombre')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('tipo')
                    ->label('Tipo de trabajador')
                    ->options(TipoTrabajador::class)
                    ->query(
                        fn(Builder $query, array $data): Builder => filled($data['value'] ?? null)
                            ? $query->whereHas('trabajador', fn(Builder $q) => $q->where('tipo', $data['value']))
                            : $query
                    ),

                Filter::make('fecha')
                    ->label('Fecha')
                    ->schema([
                        DatePicker::make('desde')->label('Desde')->native(false)->displayFormat('d/m/Y'),
                        DatePicker::make('hasta')->label('Hasta')->native(false)->displayFormat('d/m/Y'),
                    ])
                    ->columns(2)
                    ->query(
                        fn(Builder $query, array $data): Builder => $query
                            ->when($data['desde'] ?? null, fn(Builder $q, $fecha) => $q->whereDate('fecha', '>=', $fecha))
                            ->when($data['hasta'] ?? null, fn(Builder $q, $fecha) => $q->whereDate('fecha', '<=', $fecha))
                    ),
            ])

            ->recordActions([
                EditAction::make()
                    ->visible(fn(): bool => auth()->user()?->can('entregas.update') ?? false),

                DeleteAction::make()
                    ->visible(fn(): bool => auth()->user()?->can('entregas.delete') ?? false)
                    ->modalDescription('Al eliminar la entrega, los rollos entregados vuelven al stock del código.'),
            ]);
    }
}