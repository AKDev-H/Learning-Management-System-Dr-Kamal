<?php

namespace App\Filament\Admin\Resources\UserSessions;

use App\Filament\Admin\Resources\UserSessions\Pages\ListUserSessions;
use App\Filament\Admin\Resources\UserSessions\Pages\ViewUserSession;
use App\Filament\Admin\Resources\UserSessions\Schemas\UserSessionInfolist;
use App\Filament\Admin\Resources\UserSessions\Tables\UserSessionsTable;
use App\Models\UserSession;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class UserSessionResource extends Resource
{
    protected static ?string $model = UserSession::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static string|UnitEnum|null $navigationGroup = 'Access Logs';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function infolist(Schema $schema): Schema
    {
        return UserSessionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UserSessionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUserSessions::route('/'),
            'view' => ViewUserSession::route('/{record}'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
