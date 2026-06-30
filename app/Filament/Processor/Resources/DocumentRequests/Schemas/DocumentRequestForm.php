<?php

namespace App\Filament\Processor\Resources\DocumentRequests\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class DocumentRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->numeric(),
                TextInput::make('status')
                    ->required()
                    ->default('pending'),
                TextInput::make('requester_first_name')
                    ->required(),
                TextInput::make('requester_last_name')
                    ->required(),
                TextInput::make('requester_middle_name'),
                TextInput::make('requester_suffix'),
                TextInput::make('cadet_serial_number')
                    ->required(),
                TextInput::make('original_class')
                    ->required(),
                TextInput::make('contact_number')
                    ->required(),
                TextInput::make('street_address')
                    ->required(),
                TextInput::make('region')
                    ->required(),
                TextInput::make('city')
                    ->required(),
                TextInput::make('barangay')
                    ->required(),
                TextInput::make('zip_code')
                    ->required()
                    ->numeric(),
                Textarea::make('others')
                    ->columnSpanFull(),
                TextInput::make('representative_first_name'),
                TextInput::make('representative_last_name'),
                TextInput::make('representative_middle_name'),
                TextInput::make('representative_suffix'),
                TextInput::make('representative_office'),
                TextInput::make('representative_contact'),
                FileUpload::make('signature_image')
                    ->image(),
            ]);
    }
}
