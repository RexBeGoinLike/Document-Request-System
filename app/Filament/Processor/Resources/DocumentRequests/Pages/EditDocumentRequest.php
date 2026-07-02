<?php

namespace App\Filament\Processor\Resources\DocumentRequests\Pages;

use App\Filament\Processor\Resources\DocumentRequests\DocumentRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditDocumentRequest extends EditRecord
{
    protected static string $resource = DocumentRequestResource::class;

    public static bool $formActionsAreSticky = true;
    public function getHeading(): string
    {
        return 'Edit Request';
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
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
