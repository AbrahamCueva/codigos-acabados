<?php

namespace App\Filament\Resources\Actividades;

use App\Filament\Resources\Actividades\Pages\ListActividades;
use App\Filament\Resources\Actividades\Tables\ActividadesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use UnitEnum;

class ActividadResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static string|UnitEnum|null $navigationGroup = 'Sistema';

    protected static ?string $navigationLabel = 'Historial';

    protected static ?int $navigationSort = 99;

    protected static ?string $modelLabel = 'registro';

    protected static ?string $pluralModelLabel = 'historial de actividad';

    public static function table(Table $table): Table
    {
        return ActividadesTable::configure($table);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActividades::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('actividades.viewAny') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }
}