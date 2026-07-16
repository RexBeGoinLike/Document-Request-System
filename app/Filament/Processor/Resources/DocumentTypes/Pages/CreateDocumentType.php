<?php

namespace App\Filament\Processor\Resources\DocumentTypes\Pages;

use App\Filament\Processor\Resources\DocumentTypes\DocumentTypeResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateDocumentType extends CreateRecord
{
    protected static string $resource = DocumentTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back to List')
                ->color('gray')
                ->url($this->getResource()::getUrl('index')),
        ];
    }
}
