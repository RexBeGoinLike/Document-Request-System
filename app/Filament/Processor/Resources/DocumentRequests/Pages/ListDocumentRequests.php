<?php

namespace App\Filament\Processor\Resources\DocumentRequests\Pages;

use App\Filament\Processor\Resources\DocumentRequests\DocumentRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDocumentRequests extends ListRecords
{
    protected static string $resource = DocumentRequestResource::class;
    public function getHeading(): string
    {
        return '';
    }
    public function getBreadcrumbs(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make('Create')->icon('heroicon-o-plus')->label('New Request')
        ];
    }
}
