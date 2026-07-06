<?php

namespace App\Filament\Resources\MobileUsers;

use App\Filament\Resources\MobileUsers\Pages\CreateMobileUser;
use App\Filament\Resources\MobileUsers\Pages\EditMobileUser;
use App\Filament\Resources\MobileUsers\Pages\ListMobileUsers;
use App\Filament\Resources\MobileUsers\Schemas\MobileUserForm;
use App\Filament\Resources\MobileUsers\Tables\MobileUsersTable;
use App\Models\MobileUser;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class MobileUserResource extends Resource
{
    protected static ?string $model = MobileUser::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|UnitEnum|null $navigationGroup = 'Admin';

    protected static ?string $navigationLabel = 'Pengguna Aplikasi';

    protected static ?string $modelLabel = 'Pengguna Aplikasi';

    protected static ?string $pluralModelLabel = 'Pengguna Aplikasi';

    protected static ?string $recordTitleAttribute = 'nama';

    public static function form(Schema $schema): Schema
    {
        return MobileUserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MobileUsersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMobileUsers::route('/'),
            'create' => CreateMobileUser::route('/create'),
            'edit' => EditMobileUser::route('/{record}/edit'),
        ];
    }
}
