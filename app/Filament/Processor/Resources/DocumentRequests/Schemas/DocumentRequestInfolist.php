<?php

namespace App\Filament\Processor\Resources\DocumentRequests\Schemas;

use App\Models\DocumentRequest;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Group;

class DocumentRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Group::make()
                            ->schema([

                            ])
                            ->columnSpan(5)
                    ])
                    ->columns(12)
            ]);
    }
}
