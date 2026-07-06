<?php

namespace App\Filament\Guest\Resources\Tickets\Pages;

use App\Filament\Guest\Resources\Tickets\TicketResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class CreateTicket extends CreateRecord
{

    protected static string $resource = TicketResource::class;

    protected static bool $shouldRegisterNavigation = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['reference_id'] = strtoupper(Str::random(11));

        return $data;
    }

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

    protected function afterCreate(): void
    {
        $record = $this->getRecord();

        $this->replaceMountedAction('ticketCreated');
    }

    public function ticketCreatedAction(): Action
    {
        return Action::make('ticketCreated')
            ->label('Ticket Created')
            ->modalHeading('Request Pending')
            ->modalContent(fn () => new HtmlString(
                '<p class="text-center text-lg">Your Reference No. is: <strong>' . $this->getRecord()->reference_id . '</strong></p>'
            ))
            ->modalSubmitActionLabel('Go to Portal')
            ->action(function () {
                $this->redirect(filament()->getPanel('guest')->getUrl());
            })
            ->modalCancelAction(false)
            ->closeModalByClickingAway(false)
            ->modalCloseButton(false)
            ->closeModalByEscaping(false);
    }

    protected function getRedirectUrl(): string
    {
        return "";
    }
}
