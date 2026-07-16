<?php

namespace App\Filament\Processor\Resources\Users\Pages;

use App\Filament\Processor\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    public function getHeading(): string
    {
        return 'Manage Requests';
    }
    public function getBreadcrumbs(): array
    {
        return [];
    }


    protected function getHeaderActions(): array
    {
        return [

            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
