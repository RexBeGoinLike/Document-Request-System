<?php

namespace App\Filament\Processor\Resources\Users\Pages;

use App\Filament\Processor\Resources\Users\UserResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
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
            Action::make('back')
                ->label('Back to List')
                ->icon('heroicon-o-chevron-left')
                ->color('gray')
                ->url($this->getResource()::getUrl('index')),
            EditAction::make(),
        ];
    }
}
