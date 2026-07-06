<?php

namespace App\Filament\Resources\MobileUsers\Pages;

use App\Filament\Resources\MobileUsers\MobileUserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMobileUser extends EditRecord
{
    protected static string $resource = MobileUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
