<?php

namespace App\Filament\Guest\Pages;

use App\Filament\Guest\Resources\Tickets\TicketResource;
use App\Models\DocumentRequest;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;

class GuestDashboard extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected string $view = 'filament.guest.pages.guest-dashboard';

    protected static ?string $slug = '/';

    protected static ?string $title = '';

    public function getHeading(): string
    {
        return '';
    }

    public function choicesSchema(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        Section::make('Request a Document')
                            ->description('Diplomas, transcripts, certifications, and other cadet records.')
                            ->icon('heroicon-o-document-text')

                            ->footerActions([
                                Action::make('start_request')
                                    ->label('Start Request')
                                    ->url(TicketResource::getUrl('create'))
                                    ->button(),
                            ]),

                        Section::make('Track a Request')
                            ->description('Check the status of a document request using your ticket number.')
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                TextInput::make('tracking_number')
                                    ->label('Tracking Number')
                                    ->columnSpanFull(),
                            ])
                            ->footerActions([
                                Action::make('track_request')
                                    ->label('Track Status')
                                    ->color('gray')
                                    ->action(function (Get $get) {
                                        $trackingNumber = $get('tracking_number');

                                        if (blank($trackingNumber)) {
                                            Notification::make()
                                                ->title('Enter a tracking number')
                                                ->danger()
                                                ->send();

                                            return;
                                        }

                                        $ticket = DocumentRequest::find($trackingNumber);

                                        if (! $ticket) {
                                            Notification::make()
                                                ->title('Not found')
                                                ->body('No request matches that tracking number.')
                                                ->danger()
                                                ->send();

                                            return;
                                        }

                                        return redirect()->to(
                                            TicketResource::getUrl('view', ['record' => $ticket])
                                        );
                                    })
                                    ->button(),
                            ]),
                    ]),
            ]);
    }
}
