<?php

namespace App\Filament\Resources\Entregas\Schemas;

use App\Models\Codigo;
use App\Models\Trabajador;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

class EntregaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Datos de la entrega')
                    ->description('El stock del código baja al registrar la entrega. Para corregir el código o los rollos entregados, elimina la entrega y créala de nuevo.')
                    ->icon(Heroicon::OutlinedClipboardDocumentCheck)
                    ->columns(['default' => 1, 'md' => 2])
                    ->components([
                        DatePicker::make('fecha')
                            ->label('Fecha')
                            ->required()
                            ->default(now())
                            ->maxDate(now())
                            ->native(false)
                            ->displayFormat('d/m/Y'),

                        Select::make('trabajador_id')
                            ->label('Trabajador')
                            ->relationship(
                                name: 'trabajador',
                                titleAttribute: 'nombre',
                                modifyQueryUsing: fn (Builder $query, string $operation): Builder => $operation === 'create'
                                    ? $query->where('activo', true)
                                    : $query,
                            )
                            ->getOptionLabelFromRecordUsing(
                                fn (Trabajador $record): string => $record->nombre
                                    . ' · ' . $record->tipo->getLabel()
                                    . ($record->activo ? '' : ' (inactivo)')
                            )
                            ->searchable(['nombre'])
                            ->preload()
                            ->required(),

                        Select::make('codigo_id')
                            ->label('Código')
                            ->relationship(
                                name: 'codigo',
                                titleAttribute: 'codigo',
                                modifyQueryUsing: fn (Builder $query, string $operation): Builder => $operation === 'create'
                                    ? $query->where('stock', '>', 0)
                                    : $query,
                            )
                            ->getOptionLabelFromRecordUsing(
                                fn (Codigo $record): string => $record->codigo . ' (stock: ' . $record->stock . ')'
                            )
                            ->searchable(['codigo'])
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn (Get $get, Set $set) => self::calcularRestantes($get, $set))
                            ->disabledOn('edit'),

                        TextInput::make('rollos_entregados')
                            ->label('Rollos entregados')
                            ->placeholder('Ej. 2')
                            ->helperText(
                                fn (Get $get, string $operation): ?string => $operation === 'create'
                                    ? (filled($get('codigo_id'))
                                        ? 'Disponible: ' . (Codigo::find($get('codigo_id'))?->stock ?? 0)
                                        : 'Primero elige el código.')
                                    : null
                            )
                            ->required()
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->maxValue(
                                fn (Get $get, string $operation): ?int => $operation === 'create'
                                    ? Codigo::find($get('codigo_id'))?->stock
                                    : null
                            )
                            ->validationMessages([
                                'max' => 'Solo hay :max rollo(s) disponible(s) de este código.',
                            ])
                            ->live(debounce: 400)
                            ->afterStateUpdated(fn (Get $get, Set $set) => self::calcularRestantes($get, $set))
                            ->disabled(
                                fn (Get $get, string $operation): bool => $operation === 'edit' || blank($get('codigo_id'))
                            ),

                        TextInput::make('rollos_restantes')
                            ->label('Rollos restantes')
                            ->helperText('Stock del código después de esta entrega. Se calcula solo.')
                            ->placeholder('—')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false),
                    ]),
            ]);
    }

    /**
     * Rollos restantes = stock del código − rollos a entregar (en vivo).
     * Si falta algún dato o no alcanza el stock, el campo queda vacío.
     */
    protected static function calcularRestantes(Get $get, Set $set): void
    {
        $codigoId = $get('codigo_id');
        $entregados = $get('rollos_entregados');

        if (blank($codigoId) || blank($entregados)) {
            $set('rollos_restantes', null);

            return;
        }

        $restantes = (Codigo::find($codigoId)?->stock ?? 0) - (int) $entregados;

        $set('rollos_restantes', $restantes >= 0 ? $restantes : null);
    }
}