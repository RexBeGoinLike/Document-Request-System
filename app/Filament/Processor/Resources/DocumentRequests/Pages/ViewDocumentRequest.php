<?php

namespace App\Filament\Processor\Resources\DocumentRequests\Pages;

use App\Filament\Processor\Resources\DocumentRequests\DocumentRequestResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDocumentRequest extends ViewRecord
{
    protected static string $resource = DocumentRequestResource::class;

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function getHeading(): string
    {
        return 'View Request';
    }
    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
