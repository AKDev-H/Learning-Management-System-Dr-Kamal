<?php

namespace App\Filament\Admin\Resources\UserSessions\Pages;

use App\Filament\Admin\Resources\UserSessions\UserSessionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewUserSession extends ViewRecord
{
    protected static string $resource = UserSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
