<?php

namespace App\Filament\Resources\Trabajadors;

use App\Filament\Resources\Trabajadors\Pages\CreateTrabajador;
use App\Filament\Resources\Trabajadors\Pages\EditTrabajador;
use App\Filament\Resources\Trabajadors\Pages\ListTrabajadors;
use App\Filament\Resources\Trabajadors\Schemas\TrabajadorForm;
use App\Filament\Resources\Trabajadors\Tables\TrabajadorsTable;
use App\Models\Trabajador;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class TrabajadorResource extends Resource
{
    protected static ?string $model = Trabajador::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|UnitEnum|null $navigationGroup = 'Acabados';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'trabajador';

    protected static ?string $pluralModelLabel = 'trabajadores';

    protected static ?string $recordTitleAttribute = 'nombre';

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('trabajadores.viewAny') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('trabajadores.create') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('trabajadores.update') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->can('trabajadores.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return TrabajadorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrabajadorsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrabajadors::route('/'),
            'create' => CreateTrabajador::route('/create'),
            'edit' => EditTrabajador::route('/{record}/edit'),
        ];
    }
}
