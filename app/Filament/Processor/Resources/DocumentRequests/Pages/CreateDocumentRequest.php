<?php

namespace App\Filament\Processor\Resources\DocumentRequests\Pages;

use App\Filament\Processor\Resources\DocumentRequests\DocumentRequestResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateDocumentRequest extends CreateRecord
{
    protected static string $resource = DocumentRequestResource::class;



    public function getBreadcrumbs(): array
    {
        return [];
    }

}
