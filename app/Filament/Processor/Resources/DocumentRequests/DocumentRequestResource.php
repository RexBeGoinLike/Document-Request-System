<?php

namespace App\Filament\Processor\Resources\DocumentRequests;

use App\Filament\Processor\Resources\DocumentRequests\Pages\CreateDocumentRequest;
use App\Filament\Processor\Resources\DocumentRequests\Pages\EditDocumentRequest;
use App\Filament\Processor\Resources\DocumentRequests\Pages\ListDocumentRequests;
use App\Filament\Processor\Resources\DocumentRequests\Pages\ViewDocumentRequest;
use App\Filament\Processor\Resources\DocumentRequests\Schemas\DocumentRequestForm;
use App\Filament\Processor\Resources\DocumentRequests\Tables\DocumentRequestInfolist;
use App\Filament\Processor\Resources\DocumentRequests\Tables\DocumentRequestsTable;
use App\Models\DocumentRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DocumentRequestResource extends Resource
{
    protected static ?string $model = DocumentRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Manage Requests';

    public static function form(Schema $schema): Schema
    {
        return DocumentRequestForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DocumentRequestInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DocumentRequestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDocumentRequests::route('/'),
            'create' => CreateDocumentRequest::route('/create'),
            'view' => ViewDocumentRequest::route('/{record}'),
            'edit' => EditDocumentRequest::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
