<?php

namespace App\Filament\Resources\Codigos;

use App\Filament\Resources\Codigos\Pages\CreateCodigo;
use App\Filament\Resources\Codigos\Pages\EditCodigo;
use App\Filament\Resources\Codigos\Pages\ListCodigos;
use App\Filament\Resources\Codigos\Schemas\CodigoForm;
use App\Filament\Resources\Codigos\Tables\CodigosTable;
use App\Models\Codigo;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class CodigoResource extends Resource
{
    // Consultar códigos.
    public static function canViewAny(): bool
    {
        return auth()->user()?->can('codigos.viewAny') ?? false;
    }

    // Crear códigos.
    public static function canCreate(): bool
    {
        return auth()->user()?->can('codigos.create') ?? false;
    }

    // Editar códigos.
    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('codigos.update') ?? false;
    }

    // Eliminar códigos.
    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->can('codigos.delete') ?? false;
    }

    // Eliminación masiva.
    public static function canDeleteAny(): bool
    {
        return false;
    }
    protected static ?string $model = Codigo::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQrCode;

    protected static string|UnitEnum|null $navigationGroup = 'Acabados';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'código';

    protected static ?string $pluralModelLabel = 'códigos';

    protected static ?string $recordTitleAttribute = 'codigo';

    public static function form(Schema $schema): Schema
    {
        return CodigoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CodigosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCodigos::route('/'),
            'create' => CreateCodigo::route('/create'),
            'edit' => EditCodigo::route('/{record}/edit'),
        ];
    }
}