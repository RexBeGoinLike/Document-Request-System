<?php

namespace App\Filament\Guest\Resources\Tickets;

use App\Filament\Guest\Resources\ListTickets;
use App\Filament\Guest\Resources\Tickets\Pages\CreateTicket;
use App\Filament\Guest\Resources\Tickets\Pages\EditTicket;
use App\Filament\Guest\Resources\Tickets\Pages\ViewTicket;
use App\Filament\Guest\Resources\Tickets\RelationManagers\RequestQuantityRelationManager;
use App\Filament\Guest\Resources\Tickets\Schemas\TicketForm;
use App\Filament\Guest\Resources\Tickets\Schemas\TicketInfolist;
use App\Filament\Guest\Resources\Tickets\Tables\TicketsTable;
use App\Models\DocumentRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketResource extends Resource
{
    protected static ?string $model = DocumentRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Request a Document';


    public static function form(Schema $schema): Schema
    {
        return TicketForm::configure($schema);
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => CreateTicket::route('/'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    protected function afterCreate(): void
    {
        $this->mountAction('ticketCreated');
    }

    protected function getActions(): array
    {
        return [];
    }

}
