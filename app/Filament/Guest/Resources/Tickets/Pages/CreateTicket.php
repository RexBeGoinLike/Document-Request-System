<?php

namespace App\Filament\Guest\Resources\Tickets\Pages;

use App\Filament\Guest\Resources\Tickets\TicketResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    protected static bool $shouldRegisterNavigation = false;

    // 1. REMOVE BREADCRUMBS: Returns an empty list to kill the breadcrumb bar
    public function getBreadcrumbs(): array
    {
        return [];
    }

    // 2. REMOVE HEADER TEXT: Explicitly write this method to hide the H1 text
    public function getHeading(): string
    {
        return '';
    }

    protected function getFormActions(): array
    {
        return [];
    }

    // 3. REMOVE SIDEBAR LINKS: Keeps this page from registering in the sidebar navigation
    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return false;
    }
}
