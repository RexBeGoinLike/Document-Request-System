<?php

namespace App\Filament\Processor\Resources\Users\Pages;

use App\Filament\Processor\Resources\Users\UserResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
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
            Action::make('back')
                ->label('Back to List')
                ->color('gray')
                ->url($this->getResource()::getUrl('index')),
        ];
    }

    protected static string $resource = UserResource::class;
}
