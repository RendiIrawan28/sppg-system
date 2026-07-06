<?php

namespace App\Filament\Resources\MobileUsers\Pages;

use App\Filament\Resources\MobileUsers\MobileUserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMobileUsers extends ListRecords
{
    protected static string $resource = MobileUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Pengguna Aplikasi'),
        ];
    }
}
