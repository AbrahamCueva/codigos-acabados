<?php

namespace App\Filament\Resources\Entregas;

use App\Filament\Resources\Entregas\Pages\CreateEntrega;
use App\Filament\Resources\Entregas\Pages\EditEntrega;
use App\Filament\Resources\Entregas\Pages\ListEntregas;
use App\Filament\Resources\Entregas\Schemas\EntregaForm;
use App\Filament\Resources\Entregas\Tables\EntregasTable;
use App\Models\Entrega;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class EntregaResource extends Resource
{
    public static function canViewAny(): bool
    {
        return auth()->user()?->can('entregas.viewAny') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('entregas.create') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('entregas.update') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->can('entregas.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }
    protected static ?string $model = Entrega::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static string|UnitEnum|null $navigationGroup = 'Acabados';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'entrega';

    protected static ?string $pluralModelLabel = 'entregas';

    public static function form(Schema $schema): Schema
    {
        return EntregaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EntregasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEntregas::route('/'),
            'create' => CreateEntrega::route('/create'),
            'edit' => EditEntrega::route('/{record}/edit'),
        ];
    }
}