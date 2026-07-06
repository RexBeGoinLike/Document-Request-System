<?php

namespace App\Filament\Processor\Resources\DocumentTypes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DocumentTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('document_type')
                    ->required(),
                TextInput::make('document_fee')
                    ->required()
                    ->numeric(),
                TextInput::make('document_classification')
                    ->required(),
            ]);
    }
}
