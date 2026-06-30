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
use Filament\Actions\Action;
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

    public static function infolist(Schema $schema): Schema
    {
        return TicketInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TicketsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTickets::route('/'),
            'create' => CreateTicket::route('/create'),
            'view' => ViewTicket::route('/{record}'),
            'edit' => EditTicket::route('/{record}/edit'),
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
