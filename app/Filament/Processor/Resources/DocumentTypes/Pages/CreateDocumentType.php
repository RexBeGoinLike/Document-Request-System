<?php

namespace App\Filament\Processor\Resources\DocumentTypes\Pages;

use App\Filament\Processor\Resources\DocumentTypes\DocumentTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDocumentType extends CreateRecord
{
    protected static string $resource = DocumentTypeResource::class;
}
