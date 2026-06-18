<?php

namespace App\Filament\Guest\Resources\Tickets\Pages;

use App\Filament\Guest\Resources\Tickets\TicketResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;
}
